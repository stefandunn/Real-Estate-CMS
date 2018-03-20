<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnThumbnailPathToMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add column for thumbnail path
        Schema::table('media', function (Blueprint $table){

            // Add column
            $table->string('thumbnail_path', 255)->after('mobile_path')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove column for password reset token
        Schema::table('media', function (Blueprint $table){

            // Add column
            $table->dropColumn('thumbnail_path');

        });
    }
}
