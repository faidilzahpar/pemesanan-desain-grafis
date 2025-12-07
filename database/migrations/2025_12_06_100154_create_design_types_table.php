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
        Schema::create('design_types', function (Blueprint $table) {
            $table->string('design_type_id')->primary();
            $table->string('nama_jenis');
            $table->text('deskripsi');
            $table->integer('harga');
            $table->integer('durasi');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_types');
    }
};
