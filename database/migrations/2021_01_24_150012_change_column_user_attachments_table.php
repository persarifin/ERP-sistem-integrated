<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnUserAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::statement('ALTER TABLE user_attachments DROP CONSTRAINT user_attachments_type_check;');
      DB::statement('ALTER TABLE user_attachments ADD CONSTRAINT user_attachments_type_check CHECK (attachment_type::TEXT = ANY (ARRAY[\'PHOTO PROFILE\'::CHARACTER VARYING, \'ID CARD PHOTO\'::CHARACTER VARYING, \'SELFIE PHOTO\'::CHARACTER VARYING, \'TAX NUMBER PHOTO\'::CHARACTER VARYING]::TEXT[]))');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
