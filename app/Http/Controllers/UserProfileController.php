<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    // Afficher le profil de l'utilisateur connecté
    public function show()
    {
        $user = Auth::user(); // Récupère l'utilisateur connecté
        return response()->json($user, 200);
    }

    // Mettre à jour les informations de l'utilisateur
    public function update(Request $request)
    {
        $user = Auth::user(); // Récupère l'utilisateur connecté

        $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:15',
            'role' => 'string|in:tourist,owner',
        ]);

        // Mise à jour des informations de l'utilisateur
        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'phone' => $request->phone ?? $user->phone,
            'role' => $request->role ?? $user->role,
        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès !', 'user' => $user], 200);
    }

    // Supprimer le profil utilisateur (si nécessaire)
    public function destroy()
    {
        $user = Auth::user(); // Récupère l'utilisateur connecté
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès !'], 200);
    }
}
