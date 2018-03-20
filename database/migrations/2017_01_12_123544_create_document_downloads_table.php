<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_downloads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned()->index();
            $table->integer('downloads')->default(0);
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('media');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_downloads');
    }
}
