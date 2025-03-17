<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Afficher tous les avis d'un logement.
     */
    public function index($propertyId)
    {
        $reviews = Review::where('property_id', $propertyId)->with('user')->get();
        return response()->json($reviews);
    }

    /**
     * Ajouter un avis.
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $userId = Auth::id();

        // Vérifier si l'utilisateur a réservé ce logement
        if (!Review::canReview($userId, $request->property_id)) {
            return response()->json(['message' => 'Vous devez avoir réservé ce logement pour laisser un avis.'], 403);
        }

        // Vérifier si l'utilisateur a déjà laissé un avis pour ce logement
        if (Review::where('user_id', $userId)->where('property_id', $request->property_id)->exists()) {
            return response()->json(['message' => 'Vous avez déjà laissé un avis pour ce logement.'], 403);
        }

        // Créer l'avis
        $review = Review::create([
            'user_id' => $userId,
            'property_id' => $request->property_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json($review, 201);
    }

    /**
     * Modifier un avis.
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Vérifier si l'utilisateur est l'auteur de l'avis
        if ($review->user_id !== Auth::id()) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à modifier cet avis.'], 403);
        }

        $request->validate([
            'rating' => 'sometimes|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $review->update($request->only(['rating', 'comment']));

        return response()->json($review);
    }

    /**
     * Supprimer un avis.
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Vérifier si l'utilisateur est l'auteur de l'avis
        if ($review->user_id !== Auth::id()) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à supprimer cet avis.'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Avis supprimé avec succès.']);
    }
}