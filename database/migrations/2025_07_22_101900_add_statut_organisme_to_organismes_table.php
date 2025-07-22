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
        Schema::table('organismes', function (Blueprint $table) {
            $table->string('statut_organisme')->default('activé'); // 'activé' ou 'désactivé'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organismes', function (Blueprint $table) {
            $table->dropColumn('statut_organisme');
        });
    }
};
