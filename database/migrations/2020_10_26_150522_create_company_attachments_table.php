<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->enum('type', ['COMPANY_LOGO', 'ESTABLISHMENT_DEED', 'REGISTRATION_CERTIFICATE', 'BUSINESS_LICENSE', 'TAX_NUMBER_PHOTO', 'COMPANY_PROFILE']);
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
        Schema::dropIfExists('company_attachments');
    }
}
