<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Vérifier si la table est vide pour éviter les problèmes de colonnes non nulles
            $isTableEmpty = DB::table('media')->count() === 0;
            
            // Ajouter la colonne nom_media si elle n'existe pas
            if (!Schema::hasColumn('media', 'nom_media')) {
                $table->string('nom_media')->nullable();
            }
            
            // Ajouter les nouvelles colonnes
            if (!Schema::hasColumn('media', 'chemin_media')) {
                $table->string('chemin_media')->nullable();
            }
            
            if (!Schema::hasColumn('media', 'url_media')) {
                $table->string('url_media')->nullable();
            }
            
            if (!Schema::hasColumn('media', 'type_media')) {
                $table->string('type_media', 50)->default('image');
            }
            
            // Renommer la clé étrangère si nécessaire
            if (Schema::hasColumn('media', 'id_signalement') && !Schema::hasColumn('media', 'signalement_id')) {
                $table->renameColumn('id_signalement', 'signalement_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // Only rename if nom_media exists and fichier doesn't exist
            if (Schema::hasColumn('media', 'nom_media') && !Schema::hasColumn('media', 'fichier')) {
                $table->renameColumn('nom_media', 'fichier');
            }
            
            // Only rename if signalement_id exists and id_signalement doesn't exist
            if (Schema::hasColumn('media', 'signalement_id') && !Schema::hasColumn('media', 'id_signalement')) {
                $table->renameColumn('signalement_id', 'id_signalement');
            }
            
            // Drop only the columns that exist and are safe to drop
            $columnsToDrop = ['chemin_media', 'url_media', 'type_media'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('media', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
