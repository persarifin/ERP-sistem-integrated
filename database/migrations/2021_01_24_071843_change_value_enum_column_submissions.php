<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeValueEnumColumnSubmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE submissions DROP CONSTRAINT submissions_status_check;');
        DB::statement('ALTER TABLE submissions ADD CONSTRAINT submissions_status_check CHECK (status::TEXT = ANY (ARRAY[\'DRAFT\'::CHARACTER VARYING, \'PENDING\'::CHARACTER VARYING, \'REJECTED\'::CHARACTER VARYING, \'PARTIAL APPROVED\'::CHARACTER VARYING,\'APPROVED\'::CHARACTER VARYING, \'FAILED\'::CHARACTER VARYING, \'PARTIAL PAID\'::CHARACTER VARYING, \'PAID\'::CHARACTER VARYING, \'CANCELLED\'::CHARACTER VARYING, \'REFUND\'::CHARACTER VARYING, \'COMPLETED\'::CHARACTER VARYING]::TEXT[]))');
        // DB::statement("ALTER TABLE `submissions` CHANGE `status` `status` ENUM('DRAFT','PENDING','REJECTED','PARTIAL APPROVED','APPROVED','FAILED','PARTIAL PAID','PAID','CANCELLED','REFUND','COMPLETED') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::statement("ALTER TABLE `submissions` CHANGE `status` `status` ENUM('DRAFT','PENDING','REJECTED','PARTIAL APPROVED','APPROVED','FAILED','PARTIAL PAID','PAID','CANCELLED','REFUND','COMPLETED') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;");
        // DB::statement('ALTER TABLE product_attachments ADD CONSTRAINT product_attachments_type_check CHECK (type::TEXT = ANY (ARRAY[\'PRODUCT PHOTO\'::CHARACTER VARYING, \'PRODUCT CONTENT\'::CHARACTER VARYING]::TEXT[]))');
    }
}
