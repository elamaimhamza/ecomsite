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
        Schema::create('transporteurs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 'bpost_home', 'mondial_relay', etc.
            $table->string('nom');            // 'Bpost Domicile'
            $table->string('details')->nullable(); // '2-3 jours'
            $table->decimal('prix', 8, 2);    // 3.99
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transporteurs');
    }
};
