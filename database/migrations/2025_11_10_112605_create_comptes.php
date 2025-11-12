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
        Schema::create('comptes', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id'); // clé étrangère vers clients
            $table->string('numero_compte')->unique();
            $table->string('numero_user')->unique();
            $table->string('password');
            $table->string('login')->unique();
            $table->timestamps();
            // index
            $table->index(['user_id', 'numero_compte']);

            // clé étrangère
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comptes');
    }
};
