<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

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
                'message' => 'Cet utilisateur n\'est pas animateur.',
            ], 404);
        }

        return response()->json($host, 200);
    }

    public function allUsers()
    {
        $this->authorize('viewAny', User::class);

        $users = User::all();
        return response()->json($users, 200);
    }

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