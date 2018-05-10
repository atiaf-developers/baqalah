<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_method');
            /*
             1 => delivery
             2 => Receipt
            */
            $table->decimal('total_price',11,8);
            $table->float('commision',4,2);
            $table->decimal('commision_cost',11,8);

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->integer('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('stores');
            
            $table->integer('receipt_details_id')->unsigned();
            $table->foreign('receipt_details_id')->references('id')->on('receipt_details');

            $table->dateTime('date');

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
        Schema::dropIfExists('orders');
    }
}
