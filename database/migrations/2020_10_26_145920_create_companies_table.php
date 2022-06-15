<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('business_name', 100);
            $table->string('legal_name', 100);
            $table->string('tax_number', 100);
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone');
            $table->timestamp('phone_verified_at')->nullable();
            $table->text('address');
            $table->string('subdistrict', 100);
            $table->string('city', 50);
            $table->string('province', 50);
            $table->string('postal_code', 50);
            $table->string('country', 50);
            $table->text('bio');
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
        Schema::dropIfExists('companies');
    }
}
