<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('receipt_number');
			$table->decimal('total');
			$table->decimal('discount');
			$table->decimal('sub_total');
			$table->boolean('status');
			$table->timestamps();
			$table->bigInteger('customer_id')->unsigned();
			$table->decimal('payment');
			$table->decimal('balance');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}

}
