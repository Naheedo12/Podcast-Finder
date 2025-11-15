<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePodcastRequest;
use App\Http\Requests\UpdatePodcastRequest;
use App\Models\Podcast;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Tag(
 *     name="Podcasts",
 *     description="Endpoints pour la gestion des podcasts"
 * )
 */
class PodcastController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/podcasts",
     *     summary="Lister tous les podcasts",
     *     tags={"Podcasts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des podcasts récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PodcastWithUser")
     *         )
     *     )
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
     *         description="Détails du podcast récupérés avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/PodcastWithEpisodes")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast non trouvé"
     *     )
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
     *     summary="Créer un nouveau podcast",
     *     tags={"Podcasts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"titre", "categorie"},
     *                 @OA\Property(property="titre", type="string", example="Mon Podcast Tech"),
     *                 @OA\Property(property="description", type="string", example="Description de mon podcast sur la technologie"),
     *                 @OA\Property(property="categorie", type="string", example="Technology"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Image du podcast (PNG, JPG, JPEG)")
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
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs et animateurs"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
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
     * @OA\Put(
     *     path="/api/podcasts/{podcast}",
     *     summary="Modifier un podcast",
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
     *                 @OA\Property(property="description", type="string", example="Nouvelle description"),
     *                 @OA\Property(property="categorie", type="string", example="Nouvelle catégorie"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Nouvelle image du podcast")
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
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs et animateurs propriétaires"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast non trouvé"
     *     )
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
     *     summary="Supprimer un podcast",
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
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs et animateurs propriétaires"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Podcast non trouvé"
     *     )
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
     *     summary="Rechercher des podcasts",
     *     tags={"Search"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="titre",
     *         in="query",
     *         description="Rechercher par titre",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="categorie",
     *         in="query",
     *         description="Rechercher par catégorie",
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
     *         description="Résultats de la recherche",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/PodcastWithUser")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', Podcast::class);

        $query = Podcast::with('user');

        if ($request->has('titre')) {
            $query->where('titre', 'like', '%' . $request->titre . '%');
        }

        if ($request->has('categorie')) {
            $query->where('categorie', 'like', '%' . $request->categorie . '%');
        }

        if ($request->has('animateur')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->animateur . '%')
                  ->orWhere('prenom', 'like', '%' . $request->animateur . '%');
            });
        }

        $podcasts = $query->get();

        return response()->json($podcasts, 200);
    }
}