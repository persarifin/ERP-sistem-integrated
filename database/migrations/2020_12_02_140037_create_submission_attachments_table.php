<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_location');
            $table->unsignedBigInteger('submission_id');
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
        Schema::dropIfExists('submission_attachments');
    }
}
