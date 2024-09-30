<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::create('user_login_data', function (Blueprint $table) {
                $table->increments('id');
                $table->text('password');
                $table->text('email')->nullable()->unique();
                $table->timestampTz('email_verified_at')->nullable();
                $table->text('phone')->nullable()->unique();
                $table->timestampTz('phone_verified_at')->nullable();
                $table->rememberToken();
                $table->timestampsTz();
                $table->timestampTz('blocked_at')->nullable();
            });
            
            Schema::create('user_education_data', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id'); // Поле для внешнего ключа
                $table->text('educational_institute')->nullable();
                $table->text('educational_level')->nullable();
                $table->text('specialization')->nullable();
                $table->text('qualification')->nullable();
                $table->integer('start_year')->unsigned()->check('start_year >= 1970 AND start_year <= 2024')->nullable();
                $table->integer('end_year')->unsigned()->check('end_year >= 1974 AND end_year <= 2024')->nullable();
                $table->timestampsTz();
            
                $table->foreign('user_id')->references('id')->on('user_login_data')->onDelete('cascade');
            });

            Schema::create('bibliografia', function (Blueprint $table) {
                $table->id(); // Уникальный идентификатор
                $table->unsignedInteger('user_id'); 
                $table->string('journal_title')->nullable(); // Название журнала
                $table->string('journal_link')->nullable(); // Ссылка на журнал
                $table->timestamps(); // Поля created_at и updated_at

                $table->foreign('user_id')->references('id')->on('user_login_data')->onDelete('cascade');
            });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->text('token');
            // $table->timestamp('created_at')->nullable();
            $table->timestampTz('created_at')->nullable();

            $table->foreign('user_id')
                ->references('id')
                ->on('user_login_data')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });


        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('user_id')
                ->references('id')
                ->on('user_login_data')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::create('user_metadata', function (Blueprint $table) {
            // $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->primary('user_id');

            $table->text('first_name')->nullable();
            $table->text('last_name')->nullable();
            $table->text('patronymic')->nullable();
            $table->text('nickname')->nullable();
            $table->text('profile_image_uri')->nullable();
            $table->text('city')->nullable();
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->date('birthday')->nullable();
            $table->text('academic_degree')->nullable();
            $table->text('academic_title')->nullable();
            $table->text('contact_email')->nullable();


            $table->foreign('user_id')
                ->references('id')
                ->on('user_login_data')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_data');
        Schema::dropIfExists('user_metadata');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
