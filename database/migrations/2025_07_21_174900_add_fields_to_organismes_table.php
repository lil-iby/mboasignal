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
            $table->string('description_organisme')->nullable();
            $table->string('domaine_organisme')->nullable();
            $table->string('tel_organisme')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organismes', function (Blueprint $table) {
            $table->dropColumn(['description_organisme', 'domaine_organisme', 'tel_organisme']);
        });
    }
};
