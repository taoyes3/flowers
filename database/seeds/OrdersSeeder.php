<?php

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        $faker = app(Faker\Generator::class); // 获取 faker 实例
        $orders = factory(Order::class, 100)->create();

        $products = collect([]);
        foreach ($orders as $order) {
            $items = factory(OrderItem::class, random_int(1, 3))->create([
                'order_id' => $order->id,
                'rating' => $order->reviewed ? random_int(1, 5) : null,
                'review' => $order->reviewed ? $faker->sentence : null,
                'reviewed_at' => $order->reviewed ? $faker->dateTimeBetween($order->paid_at) : null,
            ]);

            // 计算总价
            $total = $items->sum(function (OrderItem $item) {
                return $item->price * $item->amount;
            });

            // 如果有优惠券，则计算优惠后的价格
            if ($order->couponCode) {
                $total = $order->couponCode->getAdjustedPrice($total);
            }

            $order->update([
                'total_amount' => $total,
            ]);

            $products = $products->merge($items->pluck('product')); // 将这笔订单的商品合并到商品合集中
        }

        $products->unique('id')->each(function (Product $product) {
            // 查出该商品的评价数、评分、销量
            $result = OrderItem::query()->where('product_id', $product->id)
                ->whereNotNull('reviewed_at')
                ->first([
                    DB::raw('count(*) as review_count'),
                    DB::raw('avg(rating) as rating'),
                ]);

            $soldCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query) {
                    $query->whereNotNull('paid_at');
                })->sum('amount');

            $product->update([
                'rating' => $result->rating ?: 5, // 如果某个商品没有评分，则默认为 5 分
                'review_count' => $result->review_count,
                'sold_count' => $soldCount,
            ]);
        });
    }

}
