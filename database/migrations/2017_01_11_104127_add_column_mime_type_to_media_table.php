<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMimeTypeToMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add colum "mime_type" to media table
        Schema::table('media', function (Blueprint $table){
            $table->string('mime_type' )->after('alt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the column "mime_type"
        Schema::table('media', function (Blueprint $table){
            $table->dropColumn(['mime_type']);
        });
    }
}
