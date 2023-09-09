<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('order_id')->unsigned();
			$table->bigInteger('unit_id')->unsigned()->nullable();
			$table->decimal('price')->unsigned();
			$table->timestamps();
			$table->bigInteger('item_id')->unsigned();
			$table->boolean('status');
			$table->decimal('unit_price');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sales');
	}

}
