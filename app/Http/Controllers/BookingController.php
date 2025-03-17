<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Un tourist peut créer une réservation
    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Vérifier si l'annonce appartient à un autre utilisateur
        $property = Property::findOrFail($request->property_id);
        if ($property->user_id == Auth::id()) {
            return response()->json(['message' => 'Vous ne pouvez pas réserver votre propre logement.'], 403);
        }

        // Vérifier si une réservation existe déjà sur cette période
        $existingBooking = Booking::where('property_id', $request->property_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
            })->exists();

        if ($existingBooking) {
            return response()->json(['message' => 'Ce logement est déjà réservé pour cette période.'], 400);
        }

        // Créer la réservation
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'property_id' => $request->property_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Réservation créée avec succès.', 'booking' => $booking], 201);
    }

    // Un tourist voit ses réservations / Un owner voit les réservations reçues
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'tourist') {
            // Le touriste voit ses propres réservations
            $bookings = Booking::where('user_id', $user->id)
                ->with('property')
                ->get();
        } else {
            // Le propriétaire voit les réservations de ses logements
            $bookings = Booking::whereHas('property', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with('user')->get();
        }

        return response()->json($bookings);
    }

    // Un tourist peut annuler une réservation en attente
    public function cancel($id)
    {
        $booking = Booking::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->where('status', 'pending')
                          ->first();

        if (!$booking) {
            return response()->json(['message' => 'Réservation introuvable ou déjà traitée.'], 404);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Réservation annulée avec succès.']);
    }

    // Un propriétaire approuve ou rejette une réservation
    public function updateStatus($id, Request $request)
    {
        $request->validate(['status' => 'required|in:confirmed,cancelled']);

        $booking = Booking::where('id', $id)
                          ->whereHas('property', function ($query) {
                              $query->where('user_id', Auth::id()); // Vérifie que le propriétaire possède le bien
                          })
                          ->where('status', 'pending')
                          ->first();

        if (!$booking) {
            return response()->json(['message' => 'Réservation introuvable ou déjà traitée.'], 404);
        }

        $booking->update([
            'status' => $request->status,
            'approved' => $request->status === 'confirmed',
        ]);

        return response()->json(['message' => 'Statut mis à jour avec succès.', 'status' => $booking->status]);
    }
}
