<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_attachments', function (Blueprint $table) {
            $table->id();
            $table->enum('type',['COVER IMAGE','CONTENT MEDIA']);
            $table->string('file_name');
            $table->string('file_location');
            $table->unsignedBigInteger('content_id');
            $table->unsignedBigInteger('company_id');
            $table->softDeletes('deleted_at', 0);            
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
        Schema::dropIfExists('content_attachments');
    }
}
