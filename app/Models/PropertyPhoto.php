<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PropertyPhoto extends Model
{
    use HasFactory;

    // Champs assignables en masse
    protected $fillable = [
        'property_id',
        'photo_url',
        'title',
        'is_primary', // Ajoute ce champ
    ];

    // Relation avec le modèle Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Accesseur pour l'URL complète de la photo
    public function getFullPhotoUrlAttribute()
    {
        return asset('storage/' . $this->photo_url);
    }

    // Validation des données
    public static function rules()
    {
        return [
            'property_id' => 'required|exists:properties,id',
            'photo_url' => 'required|string',
            'title' => 'nullable|string|max:100',
        ];
    }
}