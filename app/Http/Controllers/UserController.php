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
 *     description="Gestion des utilisateurs : CRUD, animateurs, etc."
 * )
 */


class UserController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/hosts",
 *     summary="Liste de tous les animateurs (hosts)",
 *     tags={"Users"},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Liste des animateurs",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
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
 *     summary="Afficher un animateur spécifique",
 *     tags={"Users"},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur",
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Animateur trouvé",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="L'utilisateur n'est pas un animateur"
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
 *     summary="Afficher tous les utilisateurs (Admin uniquement)",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Liste des utilisateurs",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
 *     ),
 *
 *     @OA\Response(response=403, description="Accès refusé")
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
 *     summary="Créer un nouvel utilisateur (Admin uniquement)",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nom","prenom","email","password","role"},
 *
 *             @OA\Property(property="nom", type="string", example="Dupont"),
 *             @OA\Property(property="prenom", type="string", example="Jean"),
 *             @OA\Property(property="email", type="string", example="exemple@mail.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="role", type="string", example="utilisateur")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Utilisateur créé avec succès",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *
 *     @OA\Response(response=422, description="Données invalides")
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
 *     summary="Mettre à jour un utilisateur",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur",
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="nom", type="string", example="Nouveau nom"),
 *             @OA\Property(property="prenom", type="string", example="Nouveau prénom"),
 *             @OA\Property(property="email", type="string", example="nouveau@mail.com"),
 *             @OA\Property(property="role", type="string", example="animateur")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Utilisateur mis à jour",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *
 *     @OA\Response(response=403, description="Non autorisé"),
 *     @OA\Response(response=404, description="Utilisateur introuvable")
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
 *     summary="Supprimer un utilisateur",
 *     tags={"Users"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'utilisateur",
 *         @OA\Schema(type="integer")
 *     ),
 *
 *     @OA\Response(response=200, description="Utilisateur supprimé avec succès"),
 *     @OA\Response(response=403, description="Non autorisé"),
 *     @OA\Response(response=404, description="Utilisateur introuvable")
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