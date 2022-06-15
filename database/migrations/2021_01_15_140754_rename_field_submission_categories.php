<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFieldSubmissionCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submission_categories', function (Blueprint $table) {
            $table->renameColumn('name','category_name');
            $table->renameColumn('type','submission_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submission_categories', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('type');
            $table->dropColumn('maximum');
        });
    }
}
