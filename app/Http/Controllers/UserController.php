<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function hosts()
    {
        $hosts = User::where('role', 'animateur')->get();

        return response()->json($hosts, 200);
    }

    public function showHost($id)
    {
        $host = User::findOrFail($id);

        if ($host->role !== 'animateur') {
            return response()->json([
                'message' => 'Cet utilisateur n est pas animateur.',
            ], 404);
        }

        return response()->json($host, 200);
    }

    public function allUsers()
    {
        if (auth()->user()->role !== 'administrateur') {
            return response()->json([
                'message' => 'Accès refusé : uniquement les administrateurs qui peuvent y accéder.',
            ], 403);
        }

        $users = User::all();

        return response()->json($users, 200);
    }

    public function storeUser(Request $request)
    {
        if (auth()->user()->role !== 'administrateur') {
            return response()->json([
                'message' => 'Accès refusé : uniquement pour les administrateurs.',
            ], 403);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:administrateur,animateur,utilisateur',
        ]);

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

    public function updateUser(Request $request, $id)
    {
        if (auth()->user()->role !== 'administrateur') {
            return response()->json([
                'message' => 'Accès refusé : uniquement pour les administrateurs.',
            ], 403);
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,'.$user->id,
            'role' => 'sometimes|required|in:administrateur,animateur,utilisateur',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'user' => $user,
        ], 200);
    }

    public function deleteUser($id)
    {
        if (auth()->user()->role !== 'administrateur') {
            return response()->json([
                'message' => 'Accès refusé : uniquement pour les administrateurs.',
            ], 403);
        }

        $user = User::findOrFail($id);

        if ($user->id === auth()->user()->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.',
        ], 200);
    }
}
