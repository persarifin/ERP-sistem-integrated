<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_attachments', function (Blueprint $table) {  
          $table->id();
          $table->unsignedBigInteger('user_id');
          $table->enum('type', ['PHOTO_PROFILE', 'ID_CARD_PHOTO', 'SELFIE_PHOTO', 'TAX_NUMBER_PHOTO']);
          $table->string('file_name');
          $table->string('file_location');
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
        Schema::dropIfExists('user_attachments');
    }
}
