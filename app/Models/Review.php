<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * Les attributs qui sont mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'property_id',
        'rating',
        'comment',
    ];

    /**
     * Relation : Un avis appartient à un utilisateur (touriste).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation : Un avis appartient à une annonce (logement).
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Validation : Vérifie si l'utilisateur a réservé ce logement avant de laisser un avis.
     */
    public static function canReview($userId, $propertyId)
    {
        // Vérifie si l'utilisateur a une réservation confirmée pour ce logement
        return Booking::where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->where('status', 'confirmed')
            ->exists();
    }
}