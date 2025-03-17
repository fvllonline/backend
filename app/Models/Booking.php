<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'start_date',
        'end_date',
        'status',
        'approved',
    ];

    // Relation : Un booking appartient à un utilisateur (tourist)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation : Un booking appartient à une propriété (logement)
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}