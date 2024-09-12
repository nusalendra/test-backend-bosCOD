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
        Schema::create('transaksi_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_transaksi');
            $table->foreignUuid('pengirim_id')->constrained('rekening_penggunas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('penerima_id')->constrained('rekening_penggunas')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('bank_perantara_id')->constrained('rekening_admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('biaya_admin')->default(0);
            $table->string('kode_unik')->unique();
            $table->integer('nilai_transfer');
            $table->integer('total_transfer');
            $table->timestamp('berlaku_hingga')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_transfers');
    }
};
