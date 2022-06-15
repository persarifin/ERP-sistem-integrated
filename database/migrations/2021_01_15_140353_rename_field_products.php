<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameFieldProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('name','product_name');
            $table->renameColumn('type','product_type ');
            $table->text('description')->nullable()->change();
            $table->integer('already_sold')->default(0)->change();
            $table->decimal('buying_price')->default(0)->change();
            $table->decimal('selling_price')->default(0)->change();
            $table->integer('stock')->default(0)->change();
            $table->integer('min_stock')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('description');
            $table->dropColumn('already_sold');
            $table->dropColumn('buying_price');
            $table->dropColumn('selling_price');
            $table->dropColumn('stock');
            $table->dropColumn('min_stock');
        });
    }
}
