<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeColumnProductAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::statement('ALTER TABLE product_attachments DROP CONSTRAINT product_attachments_type_check;');
      DB::statement('ALTER TABLE product_attachments ADD CONSTRAINT product_attachments_type_check CHECK (type::TEXT = ANY (ARRAY[\'PRODUCT PHOTO\'::CHARACTER VARYING, \'PRODUCT CONTENT\'::CHARACTER VARYING]::TEXT[]))');
      Schema::table('product_attachments', function (Blueprint $table) {
        $table->renameColumn('type','attachment_type');
      });
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
