<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type');
            $table->decimal('value')->comment('折扣值');
            $table->unsignedInteger('total')->comment('可兑换的数量');
            $table->unsignedInteger('used')->default(0)->comment('已兑换的数量');
            $table->decimal('min_amount', 10, 2)->comment('满足优惠券的订单金额');
            $table->dateTime('not_before')->nullable()->comment('开始时间');
            $table->dateTime('not_after')->nullable()->comment('结束时间');
            $table->boolean('enabled')->comment('是否生效');
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
        Schema::dropIfExists('coupon_codes');
    }
}
