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
        Schema::create('organismes', function (Blueprint $table) {
            $table->id('id_organisme');
            $table->string('nom_organisme');
            $table->string('contact_organisme')->nullable();
            $table->string('adresse_organisme')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organismes');
    }
};
