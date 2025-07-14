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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id('id_utilisateur');
            $table->string('nom_utilisateur');
            $table->string('prenom_utilisateur');
            $table->string('email_utilisateur')->unique();
            $table->string('pass_utilisateur');
            $table->string('type_utilisateur');
            $table->string('tel_utilisateur')->nullable();
            $table->string('tokenid')->nullable();
            $table->integer('day_token')->nullable();
            $table->integer('hour_token')->nullable();
            $table->string('etat_compte')->default('actif');
            $table->string('type_compte')->nullable();
            $table->dateTime('date_inscription')->nullable();
            $table->dateTime('date_confirmation')->nullable();
            $table->dateTime('date_suppression')->nullable();
            $table->dateTime('derniere_modification')->nullable();
            $table->boolean('statut_en_ligne')->default(false);
            $table->string('photo_utilisateur')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
