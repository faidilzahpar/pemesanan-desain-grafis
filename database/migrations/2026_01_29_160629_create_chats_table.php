<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id(); 

            $table->string('order_id'); 
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');

            $table->string('user_id');
            $table->foreign('user_id')->references('user_id')->on('users'); 

            $table->text('message')->nullable();

            $table->string('attachment')->nullable();
            
            $table->string('referenced_file_id')->nullable();
            $table->foreign('referenced_file_id')->references('file_id')->on('order_files')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
