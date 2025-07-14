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
        Schema::create('signalement_utilisateur', function (Blueprint $table) {
            $table->unsignedBigInteger('id_signalement');
            $table->unsignedBigInteger('id_utilisateur');
            $table->timestamps();
    
            $table->primary(['id_signalement', 'id_utilisateur']);
    
            $table->foreign('id_signalement')
                  ->references('id_signalement')
                  ->on('signalements')
                  ->onDelete('cascade');
    
            $table->foreign('id_utilisateur')
                  ->references('id_utilisateur')
                  ->on('utilisateurs')
                  ->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signalement_utilisateur');
    }
};
