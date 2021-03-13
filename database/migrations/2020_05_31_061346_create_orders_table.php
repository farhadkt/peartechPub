<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('matched_order_id')->nullable()->default(null);
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('delivery_date');
            $table->date('validity_date');
            $table->decimal('collateral', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->unsignedTinyInteger('type');
            $table->unsignedTinyInteger('status');
            $table->decimal('product_detail_percent', 10, 2)->default(0);
            $table->string('match_hash', 32)->nullable()->default(null);
            $table->softDeletes();
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
