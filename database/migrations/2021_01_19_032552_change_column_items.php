<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('items', function (Blueprint $table) {
        $table->decimal('buying_price', 19,4)->change();
        $table->decimal('selling_price', 19,4)->change();
        $table->decimal('quantity', 19,4)->change();
        $table->decimal('discount', 19,4)->change();
        $table->decimal('tax', 19,4)->change();
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
