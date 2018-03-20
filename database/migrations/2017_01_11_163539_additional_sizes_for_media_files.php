<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdditionalSizesForMediaFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add 3 sizes to media files
        Schema::table('media', function (Blueprint $table){

            // Add columns
            $table->string('mobile_path')->nullable()->after('path');
            $table->string('tablet_path')->nullable()->after('path');
            $table->string('desktop_path')->nullable()->after('path');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove 3 sizes to media files
        Schema::table('media', function (Blueprint $table){

            // Add columns
            $table->dropColumn(['mobile_path', 'tablet_path', 'desktop_path'])->nullable();

        });
    }
}
