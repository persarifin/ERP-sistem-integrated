<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAttributeColumnCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('subdistrict', 100)->nullable()->change();
            $table->string('city', 50)->nullable()->change();
            $table->string('province', 50)->nullable()->change();
            $table->string('postal_code', 50)->nullable()->change();
            $table->string('country', 50)->nullable()->change();
            $table->text('bio')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
}
