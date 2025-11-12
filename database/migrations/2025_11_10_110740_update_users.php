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
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('name', 'nom');
            $table->string('prenom');
            $table->string('adresse');
            $table->enum('role', ['Client','Marchand']);
            $table->enum('statut', ["Actif", "Inactif"])->default("Actif");
            $table->string('nci')->unique();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('nom', 'name');
            $table->dropColumn(['prenom', 'adresse', 'telephone', 'statut', 'nci', 'login']);
        });
    }
};
