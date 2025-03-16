<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Property;

class PropertyController extends Controller
{
    // Afficher toutes les annonces
    public function index()
    {
        $properties = Property::all();
        return response()->json($properties, 200);
    }

    // Afficher une annonce spécifique
    public function show($id)
    {
        $property = Property::find($id);
        if (!$property) {
            return response()->json(['message' => 'Annonce introuvable'], 404);
        }
        return response()->json($property, 200);
    }

    // Créer une annonce (réservé aux propriétaires)
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'owner') {
            return response()->json(['message' => 'Accès refusé'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
        ]);

        $property = Property::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'price' => $request->price,
            'city' => $request->city,
            'district' => $request->district,
        ]);

        return response()->json(['message' => 'Annonce créée avec succès', 'property' => $property], 201);
    }

    // Modifier une annonce (réservé au propriétaire de l’annonce)
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $property = Property::find($id);

        if (!$property || $property->user_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé ou annonce introuvable'], 403);
        }

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'address' => 'string|max:255',
            'price' => 'numeric|min:0',
            'city' => 'string|max:100',
            'district' => 'string|max:100',
        ]);

        $property->update($request->only(['name', 'description', 'address', 'price', 'city', 'district']));

        return response()->json(['message' => 'Annonce mise à jour avec succès', 'property' => $property], 200);
    }

    // Supprimer une annonce (réservé au propriétaire de l’annonce)
    public function destroy($id)
    {
        $user = Auth::user();
        $property = Property::find($id);

        if (!$property || $property->user_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé ou annonce introuvable'], 403);
        }

        $property->delete();
        return response()->json(['message' => 'Annonce supprimée avec succès'], 200);
    }
}