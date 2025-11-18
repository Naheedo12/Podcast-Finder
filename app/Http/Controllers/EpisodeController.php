<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\Episode;
use App\Models\Podcast;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Episodes",
 *     description="Gestion des épisodes de podcast : CRUD, recherche"
 * )
 */
class EpisodeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/podcasts/{podcast_id}/episodes",
     *     summary="Liste des épisodes d'un podcast",
     *     tags={"Episodes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="podcast_id",
     *         in="path",
     *         required=true,
     *         description="ID du podcast",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des épisodes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Episode")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Podcast introuvable"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function index($podcast_id)
    {
        $podcast = Podcast::findOrFail($podcast_id);
        $this->authorize('viewAny', Episode::class);

        $episodes = Episode::where('podcast_id', $podcast_id)->get();

        return response()->json($episodes, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/podcasts/{podcast_id}/episodes",
     *     summary="Créer un épisode (Animateur propriétaire/Admin)",
     *     tags={"Episodes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="podcast_id",
     *         in="path",
     *         required=true,
     *         description="ID du podcast",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"titre", "description", "audio", "podcast_id"},
     *                 @OA\Property(property="titre", type="string", example="Episode 1 : Introduction"),
     *                 @OA\Property(property="description", type="string", example="Premier épisode de notre podcast"),
     *                 @OA\Property(property="audio", type="string", format="binary", description="Fichier audio de l'épisode"),
     *                 @OA\Property(property="podcast_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Épisode créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Épisode créé avec succès"),
     *             @OA\Property(property="episode", ref="#/components/schemas/Episode")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Non autorisé"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(StoreEpisodeRequest $request)
    {
        $this->authorize('create', Episode::class);

        // Vérifier que l'animateur est propriétaire du podcast (sauf admin)
        if ($request->user()->role !== 'administrateur') {
            $podcast = Podcast::findOrFail($request->podcast_id);
            if ($podcast->user_id !== $request->user()->id) {
                return response()->json(['message' => 'Accès refusé : vous n\'êtes pas propriétaire de ce podcast'], 403);
            }
        }

        $infos = $request->validated();

        if ($request->hasFile('audio')) {
            $filePath = $request->file('audio')->getRealPath();
            $uploadedAudio = Cloudinary::upload($filePath, [
                'resource_type' => 'video',
            ])->getSecurePath();
            $infos['audio'] = $uploadedAudio;
        }

        $episode = Episode::create($infos);

        return response()->json([
            'message' => 'Épisode créé avec succès',
            'episode' => $episode,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/episodes/{id}",
     *     summary="Afficher les détails d'un épisode",
     *     tags={"Episodes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'épisode",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'épisode avec podcast et animateur",
     *         @OA\JsonContent(ref="#/components/schemas/Episode")
     *     ),
     *     @OA\Response(response=404, description="Épisode introuvable"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function show($id)
    {
        $episode = Episode::with('podcast.user')->findOrFail($id);
        $this->authorize('view', $episode);

        return response()->json($episode, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/episodes/{episode}",
     *     summary="Modifier un épisode (Propriétaire/Admin)",
     *     tags={"Episodes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="episode",
     *         in="path",
     *         required=true,
     *         description="ID de l'épisode",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="titre", type="string", example="Nouveau titre"),
     *                 @OA\Property(property="description", type="string", example="Nouvelle description"),
     *                 @OA\Property(property="audio", type="string", format="binary", description="Nouveau fichier audio")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Épisode modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Épisode modifié avec succès"),
     *             @OA\Property(property="episode", ref="#/components/schemas/Episode")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Non autorisé"),
     *     @OA\Response(response=404, description="Épisode introuvable")
     * )
     */
    public function update(UpdateEpisodeRequest $request, Episode $episode)
    {
        $this->authorize('update', $episode);

        $infos = $request->validated();

        if ($request->hasFile('audio')) {
            $filePath = $request->file('audio')->getRealPath();
            $uploadedAudio = Cloudinary::upload($filePath, [
                'resource_type' => 'video',
            ])->getSecurePath();
            $infos['audio'] = $uploadedAudio;
        }

        $episode->update($infos);

        return response()->json([
            'message' => 'Épisode modifié avec succès',
            'episode' => $episode,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/episodes/{episode}",
     *     summary="Supprimer un épisode (Propriétaire/Admin)",
     *     tags={"Episodes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="episode",
     *         in="path",
     *         required=true,
     *         description="ID de l'épisode",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Épisode supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Épisode supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Non autorisé"),
     *     @OA\Response(response=404, description="Épisode introuvable")
     * )
     */
    public function destroy(Episode $episode)
    {
        $this->authorize('delete', $episode);

        $episode->delete();

        return response()->json([
            'message' => 'Épisode supprimé avec succès',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/search/episodes",
     *     summary="Rechercher des épisodes par titre",
     *     tags={"Episodes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="titre",
     *         in="query",
     *         required=false,
     *         description="Recherche par titre",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de recherche",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Episode")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', Episode::class);

        $query = Episode::query();

        if ($request->has('titre')) {
            $query->where('titre', 'like', '%'.$request->titre.'%');
        }

        $episodes = $query->get();

        return response()->json($episodes, 200);
    }
}
