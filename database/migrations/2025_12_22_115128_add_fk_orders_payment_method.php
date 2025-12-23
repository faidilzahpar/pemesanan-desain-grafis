<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // pastikan tidak nullable sebelum FK
            $table->string('payment_method_id')->nullable(false)->change();

            $table->foreign('payment_method_id')
                ->references('payment_method_id')
                ->on('payment_methods')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->string('payment_method_id')->nullable()->change();
        });
    }
};
