<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
           Schema::create('type_produits', function (Blueprint $table) {
            $table->id();
            $table->enum('nom', [
                'T-shirt basique',
                'T-shirt imprimé',
                'Polo',
                'T-shirt de sport',
                'T-shirt de luxe'
            ])->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_produits');
    }
};
