<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // Ajouter un contact (owner uniquement)
    public function store(Request $request)
    {
        $request->validate([
            'contact_type' => 'required|in:phone,whatsapp,email',
            'contact_value' => 'required|string|max:255',
        ]);

        // Vérifier si l'utilisateur est un propriétaire
        if (Auth::user()->role !== 'owner') {
            return response()->json(['message' => 'Seuls les propriétaires peuvent ajouter des contacts.'], 403);
        }

        // Vérifier si ce contact existe déjà
        $existingContact = Contact::where('user_id', Auth::id())
            ->where('contact_type', $request->contact_type)
            ->first();

        if ($existingContact) {
            return response()->json(['message' => 'Ce type de contact existe déjà.'], 400);
        }

        // Ajouter le contact
        $contact = Contact::create([
            'user_id' => Auth::id(),
            'contact_type' => $request->contact_type,
            'contact_value' => $request->contact_value,
        ]);

        return response()->json(['message' => 'Contact ajouté avec succès.', 'contact' => $contact], 201);
    }

    // Voir les contacts d'un propriétaire
    public function show($user_id)
    {
        $contacts = Contact::where('user_id', $user_id)->get();

        if ($contacts->isEmpty()) {
            return response()->json(['message' => 'Aucun contact trouvé pour cet utilisateur.'], 404);
        }

        return response()->json($contacts);
    }

    // Modifier un contact (owner uniquement)
    public function update(Request $request, $id)
    {
        $request->validate([
            'contact_value' => 'required|string|max:255',
        ]);

        $contact = Contact::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$contact) {
            return response()->json(['message' => 'Contact introuvable ou non autorisé.'], 404);
        }

        $contact->update(['contact_value' => $request->contact_value]);

        return response()->json(['message' => 'Contact mis à jour avec succès.', 'contact' => $contact]);
    }

    // Supprimer un contact (owner uniquement)
    public function destroy($id)
    {
        $contact = Contact::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$contact) {
            return response()->json(['message' => 'Contact introuvable ou non autorisé.'], 404);
        }

        $contact->delete();

        return response()->json(['message' => 'Contact supprimé avec succès.']);
    }
}
