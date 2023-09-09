<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToSaleAndPurchaseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('sales', function (Blueprint $table) {
        //     $table->tinyInteger('status')->after('price');
        // });

        // Schema::table('purchases', function (Blueprint $table) {
        //     $table->tinyInteger('status')->after('price');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_and_purchase_tables', function (Blueprint $table) {
            //
        });
    }
}
