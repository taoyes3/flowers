<?php

use Faker\Generator as Faker;
use App\Models\User;
use App\Models\Order;
use App\Models\CouponCode;

$factory->define(App\Models\Order::class, function (Faker $faker) {
    $user = User::query()->inRandomOrder()->first(); // 随机取出一个用户
    $address = $user->addresses()->inRandomOrder()->first(); // 随机取出一个该用户的地址
    $refund = random_int(0, 10) < 1; // 10% 的概率把订单标记为退款
    $ship =$faker->randomElement(array_keys(Order::$shipStatusMap)); // 随机生成发货状态

    $coupon = null; // 优惠券
    // 30% 概率该订单使用了优惠券
    if (random_int(0, 10) < 3) {
        $coupon = CouponCode::query()->where('min_amount', 0)->inRandomOrder()->first(); // 为了避免出现逻辑错误，选择没有最低金额限制的优惠券
        $coupon->changeUsed(); // 增加优惠券的使用量
    }

    return [
        'address' => [
            'address' => $address->full_address,
            'zip' => $address->zip,
            'contact_name' => $address->contact_name,
            'contact_phone' => $address->contact_phone,
        ],
        'total_amount' => 0,
        'remark' => $faker->sentence,
        'paid_at' => $faker->dateTimeBetween('-30 days'),  // 30 天前到现在任意时间点
        'payment_method' => $faker->randomElement(['alipay', 'wechat']),
        'payment_no' => $faker->uuid,
        'refund_status' => $refund ? Order::REFUND_STATUS_SUCCESS : Order::REFUND_STATUS_PENDING,
        'refund_no' => $refund ? Order::findAvailableNo() : null,
        'closed' => false,
        'reviewed' => random_int(0, 10) < 2,
        'ship_status' => $ship,
        'ship_data' => $ship === Order::SHIP_STATUS_PENDING ? null : [
            'express_company' => $faker->company,
            'express_no' => $faker->uuid,
        ],
        'extra' => $refund ? ['refund_reason' => $faker->sentence] : [],
        'user_id' => $user->id,
        'coupon_code_id' => $coupon ? $coupon->id : null,
    ];
});
