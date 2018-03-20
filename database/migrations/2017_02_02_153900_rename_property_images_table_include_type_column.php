<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenamePropertyImagesTableIncludeTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // Remove foreigns
        Schema::table('property_images', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropForeign(['property_id']);
        });

        // Rename table
        Schema::rename('property_images', 'property_files');

        // Add column
        Schema::table('property_files', function (Blueprint $table) {
            $table->string('type')->default('image')->after('image_id');
            $table->renameColumn('image_id', 'file_id');

            // Add foreigns
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('file_id')->references('id')->on('media');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::disableForeignKeyConstraints();

        // Remove foreigns
        Schema::table('property_files', function (Blueprint $table) {
            $table->dropForeign(['file_id']);
            $table->dropForeign(['property_id']);
        });

        // Rename table
        Schema::rename('property_files', 'property_images');

        // Remove column
        Schema::table('property_images', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->renameColumn('file_id', 'image_id');

            // Add foreigns
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('image_id')->references('id')->on('media');
        });

        Schema::enableForeignKeyConstraints();
    }
}
