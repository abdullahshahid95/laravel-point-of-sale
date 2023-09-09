<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePurchasesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchases', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('item_id')->unsigned();
			$table->bigInteger('purchase_unit_id')->unsigned();
			$table->decimal('price')->unsigned();
			$table->timestamps();
			$table->boolean('status');
			$table->boolean('is_returned')->default(0);
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
		Schema::drop('purchases');
	}

}
