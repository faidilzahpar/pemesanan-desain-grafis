<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // HAPUS KOLOM LAMA (jika ada)
            if (Schema::hasColumn('orders', 'metode_pembayaran')) {
                $table->dropColumn('metode_pembayaran');
            }

            // TAMBAH KOLOM BARU (BELUM FK)
            $table->string('payment_method_id')->nullable()->after('design_type_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_method_id');

            // rollback kolom lama (opsional)
            $table->string('metode_pembayaran')->nullable();
        });
    }
};
