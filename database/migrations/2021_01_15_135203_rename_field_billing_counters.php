<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFieldBillingCounters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing_counters', function (Blueprint $table) {
            $table->renameColumn('name','counter_name');
            $table->decimal('amount', 10,2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing_counters', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('amount');
        });
    }
}
