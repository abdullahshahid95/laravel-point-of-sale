<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("name");
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->unsignedBigInteger("category_id")->default(1);
            $table->dropColumn('unit');
            $table->unsignedBigInteger('unit_id');
            $table->string('image');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('unit_id');
            $table->decimal('quantity', 8, 2);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('purchase_unit_id');
            $table->decimal('quantity', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
