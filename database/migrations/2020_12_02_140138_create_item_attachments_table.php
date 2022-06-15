<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_location');
            $table->unsignedBigInteger('item_id');
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
        Schema::dropIfExists('item_attachments');
    }
}
