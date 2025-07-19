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
        Schema::table('signalements', function (Blueprint $table) {
            // Supprimer d'abord la contrainte de clé étrangère existante
            $table->dropForeign(['id_organisme']);
            
            // Rendre la colonne nullable
            $table->unsignedBigInteger('id_organisme')->nullable()->change();
            
            // Recréer la contrainte de clé étrangère avec onDelete('set null' pour gérer la suppression de l'organisme
            $table->foreign('id_organisme')
                  ->references('id_organisme')
                  ->on('organismes')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            // Supprimer la contrainte de clé étrangère
            $table->dropForeign(['id_organisme']);
            
            // Rendre la colonne non nullable
            $table->unsignedBigInteger('id_organisme')->nullable(false)->change();
            
            // Recréer la contrainte de clé étrangère d'origine
            $table->foreign('id_organisme')
                  ->references('id_organisme')
                  ->on('organismes')
                  ->onDelete('cascade');
        });
    }
};
