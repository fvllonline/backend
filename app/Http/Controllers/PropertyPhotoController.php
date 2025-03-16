<?php

namespace App\Http\Controllers;

use App\Models\PropertyPhoto;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyPhotoController extends Controller
{
    // Ajouter une photo à une annonce
    // Dans PropertyPhotoController.php

// Ajouter une photo
public function store(Request $request, $property_id)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'title' => 'nullable|string|max:100',
    ]);

    $photoPath = $request->file('photo')->store('property_photos', 'public');

    $photo = PropertyPhoto::create([
        'property_id' => $property_id,
        'photo_url' => $photoPath,
        'title' => $request->title,
    ]);

    return response()->json([
        'message' => 'Photo ajoutée avec succès !',
        'photo' => $photo,
        'full_photo_url' => $photo->full_photo_url, // Utilisation de l'accesseur
    ], 201);
}

// Récupérer les photos
    public function index($property_id)
    {
        $photos = PropertyPhoto::where('property_id', $property_id)->get();

        if ($photos->isEmpty()) {
        return response()->json(['message' => 'Aucune photo trouvée pour cette annonce.'], 404);
        }

        return response()->json($photos->map(function ($photo) {
        return [
            'id' => $photo->id,
            'title' => $photo->title,
            'photo_url' => $photo->full_photo_url, // Utilisation de l'accesseur
        ];
        }), 200);
    }

    // Supprimer une photo
    public function destroy($id)
    {
        $photo = PropertyPhoto::findOrFail($id);

        // Supprimer le fichier du stockage
        Storage::disk('public')->delete($photo->photo_url);

        // Supprimer l'entrée de la base de données
        $photo->delete();

        return response()->json(['message' => 'Photo supprimée avec succès !'], 200);
    }
}