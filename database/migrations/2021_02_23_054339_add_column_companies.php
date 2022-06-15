<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('tagline')->nullable();
            $table->text('sub_tagline')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->text('work_culture')->nullable();
            $table->text('working_space')->nullable();
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
            $table->dropColumn('tagline');
            $table->dropColumn('sub_tagline');
            $table->dropColumn('vision');
            $table->dropColumn('mission');
            $table->dropColumn('work_culture');
            $table->dropColumn('working_space');
        });
    }
}
