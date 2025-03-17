<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'contact_type', 'contact_value'];

    // Relation : Un contact appartient à un utilisateur (owner)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}