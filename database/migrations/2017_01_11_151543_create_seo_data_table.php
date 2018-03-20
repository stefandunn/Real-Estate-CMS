<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->boolean('no_index')->default(0)->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_type')->nullable();
            $table->integer('og_image_id')->unsigned()->nullable()->index();
            $table->string('og_url')->nullable();
            $table->string('og_site_name')->nullable();
            $table->string('twitter_site')->nullable();
            $table->string('twitter_creator')->nullable();
            $table->integer('twitter_image_id')->nullable()->unsigned()->index();
            $table->text('tracking_code')->nullable();
            $table->timestamps();

            $table->foreign('twitter_image_id')->references('id')->on('media');
            $table->foreign('og_image_id')->references('id')->on('media');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo_data');
    }
}
