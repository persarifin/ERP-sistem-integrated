<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFieldItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->renameColumn('name','item_name');
            $table->integer('quantity')->default(0)->change();
            $table->decimal('buying_price')->default(0)->change();
            $table->decimal('selling_price')->default(0)->change();
            $table->decimal('discount')->default(0)->change();
            $table->decimal('tax')->default(0)->change();
            $table->unsignedBigInteger('product_id')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->dropColumn('already_sold');
            $table->dropColumn('buying_price');
            $table->dropColumn('product_id');
            $table->dropColumn('selling_price');
        });
    }
}
