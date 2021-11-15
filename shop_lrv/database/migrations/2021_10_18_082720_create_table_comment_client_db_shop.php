<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCommentClientDbShop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Comment_clients', function (Blueprint $table) {
            $table->id();
            $table->integer('Cm_user_id');
            $table->integer('Cm_product_id');
            $table->string('Content');
            $table->dateTime('Dtime');
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
        Schema::dropIfExists('Comment_clients');
    }
}
