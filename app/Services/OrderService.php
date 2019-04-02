<?php

namespace App\Services;

use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;
use App\Exceptions\InvalidRequestException;

class OrderService
{
    public function store(User $user, UserAddress $address, $remark, $items)
    {
        // 开启数据库事物
        $order = \DB::transaction(function () use ($user, $address, $remark, $items) {
            // 更新用户地址最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);

            // 创建订单
            $order = new Order([
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $remark,
                'total_amount' => 0,
            ]);
            // 订单关联当前用户
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;

            // 遍历用户提交的 SKU
            foreach ($items as $item) {
                $productSku = ProductSku::find($item['sku_id']);

                // 此处不可用 create ，因为 create 方法内会执行 save 方法
                $orderItem = $order->items()->make([
                    'amount' => $item['amount'],
                    'price' => $productSku->price,
                ]);
                $orderItem->product()->associate($productSku->product);
                $orderItem->productSku()->associate($productSku);
                $orderItem->save();
                $totalAmount += $productSku->price * $item['amount'];

                if ($productSku->decreaseStock($item['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车移除
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        // 这里直接使用 dispatch 函数
        dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
    }
}