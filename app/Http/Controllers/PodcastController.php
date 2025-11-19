<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePodcastRequest;
use App\Http\Requests\UpdatePodcastRequest;
use App\Models\Podcast;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Podcasts",
 *     description="Gestion des podcasts : CRUD, recherche"
 * )
 */
class PodcastController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/podcasts",
     *     summary="Liste de tous les podcasts",
     *     tags={"Podcasts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des podcasts avec leurs animateurs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Podcast")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function index()
    {
        $this->authorize('viewAny', Podcast::class);

        $podcasts = Podcast::with('user')->get();

        return response()->json($podcasts);
    }

    /**
     * @OA\Get(
     *     path="/api/podcasts/{id}",
     *     summary="Afficher les détails d'un podcast",
     *     tags={"Podcasts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du podcast",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du podcast avec animateur et épisodes",
     *         @OA\JsonContent(ref="#/components/schemas/Podcast")
     *     ),
     *     @OA\Response(response=404, description="Podcast introuvable"),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function show($id)
    {
        $podcast = Podcast::with(['user', 'episodes'])->findOrFail($id);

        $this->authorize('view', $podcast);

        return response()->json($podcast, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/podcasts",
     *     summary="Créer un nouveau podcast (Animateur/Admin)",
     *     tags={"Podcasts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"titre", "categorie", "description"},
     *                 @OA\Property(property="titre", type="string", example="Mon Podcast Tech"),
     *                 @OA\Property(property="categorie", type="string", example="Technologie"),
     *                 @OA\Property(property="description", type="string", example="Un podcast sur les nouvelles technologies"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Image du podcast")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Podcast créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Podcast créé avec succès"),
     *             @OA\Property(property="podcast", ref="#/components/schemas/Podcast")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Non autorisé"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(StorePodcastRequest $request)
    {
        $this->authorize('create', Podcast::class);

        $infos = $request->validated();

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->getRealPath();
            $uploadedImage = Cloudinary::upload($filePath)->getSecurePath();
            $infos['image'] = $uploadedImage;
        }

        $podcast = $request->user()->podcasts()->create($infos);

        return response()->json([
            'message' => 'Podcast créé avec succès',
            'podcast' => $podcast,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/podcasts/{podcast}",
     *     summary="Modifier un podcast (Propriétaire/Admin)",
     *     tags={"Podcasts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="podcast",
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
     *                 @OA\Property(property="titre", type="string", example="Nouveau titre"),
     *                 @OA\Property(property="categorie", type="string", example="Science"),
     *                 @OA\Property(property="description", type="string", example="Nouvelle description"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Nouvelle image")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Podcast modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Podcast modifié avec succès"),
     *             @OA\Property(property="podcast", ref="#/components/schemas/Podcast")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Non autorisé"),
     *     @OA\Response(response=404, description="Podcast introuvable")
     * )
     */
    public function update(UpdatePodcastRequest $request, Podcast $podcast)
    {
        $this->authorize('update', $podcast);

        $infos = $request->validated();

        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->getRealPath();
            $uploadedImage = Cloudinary::upload($filePath)->getSecurePath();
            $infos['image'] = $uploadedImage;
        }

        $podcast->update($infos);

        return response()->json([
            'message' => 'Podcast modifié avec succès',
            'podcast' => $podcast,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/podcasts/{podcast}",
     *     summary="Supprimer un podcast (Propriétaire/Admin)",
     *     tags={"Podcasts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="podcast",
     *         in="path",
     *         required=true,
     *         description="ID du podcast",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Podcast supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Podcast supprimé avec succès")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Non autorisé"),
     *     @OA\Response(response=404, description="Podcast introuvable")
     * )
     */
    public function destroy(Podcast $podcast)
    {
        $this->authorize('delete', $podcast);

        $podcast->delete();

        return response()->json([
            'message' => 'Podcast supprimé avec succès',
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/search/podcasts",
     *     summary="Rechercher des podcasts par titre ou catégorie",
     *     tags={"Podcasts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="titre",
     *         in="query",
     *         required=false,
     *         description="Recherche par titre",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="categorie",
     *         in="query",
     *         required=false,
     *         description="Recherche par catégorie",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Résultats de recherche",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Podcast")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié")
     * )
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', Podcast::class);

        $query = Podcast::query();

        if ($request->has('titre')) {
            $query->where('titre', 'like', '%'.$request->titre.'%');
        }

        if ($request->has('categorie')) {
            $query->where('categorie', 'like', '%'.$request->categorie.'%');
        }

        if($request->has('description')){
            $query->where('description', 'like', '%'.$request->description. '%' );
        }

        $podcasts = $query->get();

        return response()->json($podcasts, 200);
    }
}

