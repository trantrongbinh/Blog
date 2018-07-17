<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->increments('iduser');
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', array('user', 'collaborator', 'admin'));
            $table->integer('point')->default(0);
            $table->date('birthday')->nullable();
            $table->string('job')->nullable();
            $table->string('education')->nullable();
            $table->string('address')->nullable();
            $table->text('about')->nullable();
            $table->text('skill')->nullable();
            $table->boolean('valid')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->string('confirmation_code')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
