<?php

use Faker\Generator as Faker;

$factory->define(App\Models\OrderItem::class, function (Faker $faker) {
    $product = \App\Models\Product::query()->where('on_sale', true)->inRandomOrder()->first(); // 随机取出一条商品
    $sku = $product->skus()->inRandomOrder()->first(); // 从该商品的 sku 中随机取出一条

    return [
        'amount' => random_int(1, 5),
        'price' => $sku->price,
        'rating' => null,
        'review' => null,
        'reviewed_at' => null,
        'product_id' => $product->id,
        'product_sku_id' => $sku->id,
    ];
});
