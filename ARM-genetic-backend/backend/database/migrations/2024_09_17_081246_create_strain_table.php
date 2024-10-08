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

        Schema::create('analyze_strain', function (Blueprint $table) {
            $table->increments('id');
            $table->timestampsTz();
            $table->integer('author_id')->unsigned();
            $table->integer('strain_id')->unsigned(); // Связь с таблицей strain
            $table->text('repeat_sequence');
            $table->json('repeat_positions'); // Используем JSON для хранения массивов
            $table->text('spacer_sequence');
            $table->json('spacer_positions'); // Используем JSON для хранения массивов
            $table->boolean('is_known')->default(false);
            $table->string('status')->default('pending'); // Поле для статуса анализа
            $table->text('full_context');

            $table->foreign('author_id')->references('id')->on('user_login_data')->onDelete('cascade');
            $table->foreign('strain_id')->references('id')->on('strain')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('strain');
        Schema::dropIfExists('analyze_strain');
    }
};
