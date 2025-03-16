<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
            $table->string('photo_url', 255); // Longueur maximale de 255 caractères
            $table->string('title', 100)->nullable(); // Exemple: "Cuisine", "Chambre1"
            $table->boolean('is_primary')->default(false); // Photo principale (true/false)
            $table->timestamps();

            // Index sur property_id pour optimiser les requêtes
            $table->index('property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_photos');
    }
};