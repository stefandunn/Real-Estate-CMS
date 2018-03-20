<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeImagesIntoMediaManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Rename images table to media table
        Schema::rename('images', 'media');

        // Change foreign key references
        Schema::table('properties', function (Blueprint $table) {

            // Update foreign key references from "images" to "media" table
            $table->dropForeign(['feature_image_id']);

            // Add new foreign keys
            $table->foreign('feature_image_id')->references('id')->on('media');
        });

        // Change foreign key references
        Schema::table('property_images', function (Blueprint $table) {

            // Update foreign key references from "images" to "media" table
            $table->dropForeign(['image_id']);

            // Add new foreign keys
            $table->foreign('image_id')->references('id')->on('media');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Rename back
        Schema::rename('media', 'images');

        // Redo foreign keys
        Schema::table('properties', function (Blueprint $table) {

            // Update foreign key references from "images" to "media" table
            $table->dropForeign(['feature_image_id']);

            // Add new foreign keys
            $table->foreign('feature_image_id')->references('id')->on('images');
        });

        Schema::table('property_images', function(Blueprint $table){

            // Drop renamed foreign
            $table->dropForeign(['image_id']);

            // Reassign old foreign key to images table
            $table->foreign('image_id')->references('id')->on('images');
        });
    }
}
