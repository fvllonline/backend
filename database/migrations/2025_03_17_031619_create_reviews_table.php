<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Touriste
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade'); // Logement
            $table->integer('rating')->checkBetween(1, 5); // Note entre 1 et 5
            $table->text('comment')->nullable(); // Commentaire optionnel
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};