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
        Schema::create('strain', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name')->unique();
            $table->text('link');
            $table->text('place_of_allocation');
            $table->text('year_of_allocation');
            $table->text('type_of_bacteria');
            $table->timestampsTz();
            $table->integer('author_id')->unsigned();

            $table->foreign('author_id')->references('id')->on('user_login_data')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strain');
    }
};
