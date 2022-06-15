<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAttachmentTypeToSubmissionAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submission_attachments', function (Blueprint $table) {
            $table->enum('attachment_type', ['SUBMISSION PHOTO','SUBMISSION CONTENT'])->after('file_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submission_attachments', function (Blueprint $table) {
            //
        });
    }
}
