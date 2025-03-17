<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Propriétaire
            $table->enum('contact_type', ['phone', 'whatsapp', 'email']);
            $table->string('contact_value');
            $table->timestamps();

            $table->unique(['user_id', 'contact_type']); // Un propriétaire ne peut avoir qu'un seul contact de chaque type
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
