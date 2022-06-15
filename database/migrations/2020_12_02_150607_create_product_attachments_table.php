<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_location');
            $table->enum('type',['PRODUCT FOTO','PRODUCT CONTENT']);
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('product_id');
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
        Schema::dropIfExists('product_attachments');
    }
}
