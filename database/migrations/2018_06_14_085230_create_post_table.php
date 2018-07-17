<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('topic_id')->unsigned()->nullable();
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
            $table->string('slug_title')->unique();
            $table->string('seo_title')->nullable();
            $table->text('description')->nullable();
            $table->longText('content_post');
            $table->text('meta_des');
            $table->text('meta_keyword');
            $table->boolean('active')->default(false);
            $table->string('url_img')->nullable();
            $table->integer('view')->default(0);
            $table->tinyInteger('type')->default(1);
            $table->integer('parent')->nullable();
            $table->integer('rate')->default(0);
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
        Schema::dropIfExists('posts');
    }
}
