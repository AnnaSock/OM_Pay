<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TypeTransaction;
use App\Enums\StatutTransaction;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('montant', 15, 2);
            $table->enum('type_transaction', ['Depot', 'Payement', 'Retrait'])->index();
            $table->uuid('compte_id')->index();
            $table->foreign('compte_id')->references('id')->on('comptes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
