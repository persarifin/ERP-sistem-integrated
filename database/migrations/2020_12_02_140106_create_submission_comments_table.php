<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment');
            $table->date('date');
            $table->unsignedBigInteger('submission_id');
            $table->unsignedBigInteger('company_id');
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
        Schema::dropIfExists('submission_comments');
    }
}
