<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Tag(
 *     name="Episodes",
 *     description="Endpoints pour la gestion des épisodes"
 * )
 */
class EpisodeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/podcasts/{podcast_id}/episodes",
     *     summary="Lister les épisodes d'un podcast",
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
     *         description="Liste des épisodes récupérée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast non trouvé"
     *     )
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
     *     summary="Créer un nouvel épisode",
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
     *                 required={"titre", "audio"},
     *                 @OA\Property(property="titre", type="string", example="Mon premier épisode"),
     *                 @OA\Property(property="description", type="string", example="Description de l'épisode"),
     *                 @OA\Property(property="audio", type="string", format="binary", description="Fichier audio (MP3, WAV)"),
     *                 @OA\Property(property="podcast_id", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Épisode créé avec succès"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     )
     * )
     */
    public function store(StoreEpisodeRequest $request)
    {
        $this->authorize('create', Episode::class);

        if ($request->user()->role !== 'administrateur') {
            $podcast = Podcast::findOrFail($request->podcast_id);
            if (!Gate::allows('own-podcast', $podcast)) {
                return response()->json(['message' => 'Accès refusé : vous n\'êtes pas propriétaire de ce podcast'], 403);
            }
        }

        $infos = $request->validated();

        if ($request->hasFile('audio')) {
            $filePath = $request->file('audio')->getRealPath();
            $uploadedAudio = Cloudinary::upload($filePath, [
                'resource_type' => 'video'
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
     *         description="Détails de l'épisode récupérés avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Épisode non trouvé"
     *     )
     * )
     */
    public function show($id)
    {
        $episode = Episode::with('podcast.user')->findOrFail($id);
        $this->authorize('view', $episode);

        return response()->json($episode, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/episodes/{episode}",
     *     summary="Modifier un épisode",
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
     *                 @OA\Property(property="titre", type="string", example="Titre modifié"),
     *                 @OA\Property(property="description", type="string", example="Description modifiée"),
     *                 @OA\Property(property="audio", type="string", format="binary", description="Nouveau fichier audio")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Épisode modifié avec succès"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     )
     * )
     */
    public function update(UpdateEpisodeRequest $request, Episode $episode)
    {
        $this->authorize('update', $episode);

        $infos = $request->validated();

        if ($request->hasFile('audio')) {
            $filePath = $request->file('audio')->getRealPath();
            $uploadedAudio = Cloudinary::upload($filePath, [
                'resource_type' => 'video'
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
     *     summary="Supprimer un épisode",
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
     *         description="Épisode supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé"
     *     )
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
     *     summary="Rechercher des épisodes",
     *     tags={"Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="titre",
     *         in="query",
     *         description="Rechercher par titre d'épisode",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="podcast",
     *         in="query",
     *         description="Rechercher par titre de podcast",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="animateur",
     *         in="query",
     *         description="Rechercher par nom d'animateur",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de la recherche"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', Episode::class);

        $query = Episode::with('podcast.user');

        if ($request->has('titre')) {
            $query->where('titre', 'like', '%' . $request->titre . '%');
        }

        if ($request->has('podcast')) {
            $query->whereHas('podcast', function ($q) use ($request) {
                $q->where('titre', 'like', '%' . $request->podcast . '%');
            });
        }

        if ($request->has('animateur')) {
            $query->whereHas('podcast.user', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->animateur . '%')
                  ->orWhere('prenom', 'like', '%' . $request->animateur . '%');
            });
        }

        $episodes = $query->get();

        return response()->json($episodes, 200);
    }
}