<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="Endpoints pour la gestion des utilisateurs (Admin seulement)"
 * )
 * @OA\Tag(
 *     name="Hosts",
 *     description="Endpoints pour les animateurs"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/hosts",
     *     summary="Lister tous les animateurs",
     *     tags={"Hosts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des animateurs récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     )
     * )
     */
    public function hosts()
    {
        $hosts = User::where('role', 'animateur')->get();
        return response()->json($hosts, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/hosts/{id}",
     *     summary="Afficher les détails d'un animateur",
     *     tags={"Hosts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'animateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de l'animateur récupérés avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animateur non trouvé ou utilisateur n'est pas un animateur"
     *     )
     * )
     */
    public function showHost($id)
    {
        $host = User::findOrFail($id);

        if ($host->role !== 'animateur') {
            return response()->json([
                'message' => 'Cet utilisateur n\'est pas animateur.',
            ], 404);
        }

        return response()->json($host, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Lister tous les utilisateurs (Admin seulement)",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs"
     *     )
     * )
     */
    public function allUsers()
    {
        $this->authorize('viewAny', User::class);

        $users = User::all();
        return response()->json($users, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Créer un nouvel utilisateur (Admin seulement)",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom", "prenom", "email", "password", "role"},
     *             @OA\Property(property="nom", type="string", example="Martin"),
     *             @OA\Property(property="prenom", type="string", example="Pierre"),
     *             @OA\Property(property="email", type="string", format="email", example="pierre@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"administrateur", "animateur", "utilisateur"}, example="animateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur créé avec succès."),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function storeUser(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'user' => $user,
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Modifier un utilisateur (Admin seulement)",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nom", type="string", example="Nouveau nom"),
     *             @OA\Property(property="prenom", type="string", example="Nouveau prénom"),
     *             @OA\Property(property="email", type="string", format="email", example="nouveau@example.com"),
     *             @OA\Property(property="role", type="string", enum={"administrateur", "animateur", "utilisateur"}, example="administrateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur mis à jour avec succès."),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function updateUser(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        
        $this->authorize('update', $user);

        $validated = $request->validated();

        $user->update($validated);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'user' => $user,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Supprimer un utilisateur (Admin seulement)",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'utilisateur",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur supprimé avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Impossible de supprimer son propre compte"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Réservé aux administrateurs"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé"
     *     )
     * )
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.',
        ], 200);
    }
}