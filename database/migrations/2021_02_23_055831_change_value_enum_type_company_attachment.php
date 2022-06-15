<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeValueEnumTypeCompanyAttachment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE company_attachments DROP CONSTRAINT company_attachments_type_check;');
        DB::statement('ALTER TABLE company_attachments ADD CONSTRAINT company_attachments_type_check CHECK (type::TEXT = ANY (ARRAY[\'COMPANY_LOGO\'::CHARACTER VARYING, \'ESTABLISHMENT_DEED\'::CHARACTER VARYING, \'REGISTRATION_CERTIFICATE\'::CHARACTER VARYING, \'BUSINESS_LICENSE\'::CHARACTER VARYING,\'TAX_NUMBER_PHOTO\'::CHARACTER VARYING, \'COMPANY_PROFILE\'::CHARACTER VARYING, \'MAIN_IMAGE\'::CHARACTER VARYING, \'SOLUTION_IMAGE\'::CHARACTER VARYING, \'OFFICE_IMAGE\'::CHARACTER VARYING, \'TERMS_AND_CONDITION\'::CHARACTER VARYING, \'PRIVACY_POLICY\'::CHARACTER VARYING]::TEXT[]))');

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
