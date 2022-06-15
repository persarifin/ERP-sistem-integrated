<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->date('due_date');
            $table->text('description')->nullable();
            $table->string('reference_doc_number')->nullable();
            $table->enum('status',['DRAFT','PENDING','PARTIAL APPROVED','APPROVED','FAILED','PARTIAL PAID','PAID','CANCELLED','REFUND','COMPLETE']);
            $table->boolean('fullfilment')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('partner_id');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('submissions');
    }
}
