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
        Schema::create('signalements', function (Blueprint $table) {
            $table->id('id_signalement');
            $table->string('nom_signalement');
            $table->text('description_signalement');
            $table->dateTime('date_enregistrement');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('etat_signalement')->default('nouveau');
            $table->dateTime('date_modification')->nullable();
            $table->string('statut_signalement')->default('non traité');
            $table->unsignedBigInteger('id_categorie');
            $table->unsignedBigInteger('id_organisme');

            $table->foreign('id_categorie')
                ->references('id_categorie')
                ->on('categories')
                ->onDelete('cascade');

            $table->foreign('id_organisme')
                ->references('id_organisme')
                ->on('organismes')
                ->onDelete('cascade');

            // Clés étrangères à ajouter plus tard (catégorie, organisme)
            
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};
