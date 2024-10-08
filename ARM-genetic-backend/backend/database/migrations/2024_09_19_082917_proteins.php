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
        Schema::create('proteins', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->text('link');
            $table->timestampsTz();
            $table->integer('author_id')->unsigned();
            $table->integer('strain_id')->unsigned();

            $table->foreign('author_id')->references('id')->on('user_login_data')->onDelete('cascade');
            $table->foreign('strain_id')->references('id')->on('strain')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proteins');
    }
};
