<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableDetailOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('DetailOd', function (Blueprint $table) {
            $table->id();
            $table->integer('Dt_order_id');
            $table->integer('Dt_product_id');
            $table->string('Dt_quantity');
            $table->string('Dt_color');
            $table->string('Dt_price');
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
        Schema::dropIfExists('DetailOd');
    }
}
