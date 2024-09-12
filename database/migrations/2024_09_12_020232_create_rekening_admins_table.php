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
        Schema::create('rekening_admins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bank_id')->constrained('banks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('atas_nama');
            $table->string('nomor_rekening');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekening_admins');
    }
};
