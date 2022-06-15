<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type',['NO LIMIT','TIME LIMIT','STOCK LIMIT','LIMITED']);
            $table->text('description');
            $table->integer('stock');
            $table->integer('min_stock');
            $table->integer('already_sold');
            $table->decimal('buying_price');
            $table->decimal('selling_price');
            $table->enum('status',['PENDING','PARTIAL APPROVED','APPROVED']);
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('category_id');
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
        Schema::dropIfExists('products');
    }
}
