<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class RenameColumnSubmissionCategoryMaximums extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE submission_categories ALTER COLUMN 
                  maximum TYPE integer USING maximum::integer');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submission_categories', function (Blueprint $table) {
            $table->drop('maximum');
        });
    }
}
