<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * CloseOrder constructor.
     * @param Order $order
     * @param $delay
     */
    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // 设置延迟时间，delay() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * @throws \Throwable
     */
    public function handle()
    {
        // 如果订单已支付，则退出
        if ($this->order->paid_at) {
            return;
        }

        // 通过事物执行sql
        \DB::transaction(function () {
            // 关闭订单
            $this->order->update(['closed' => true]);

            // 将订单中的数量加回到 SKU 库
            foreach ($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }

            if ($this->order->couponCode) {
                $this->order->couponCode->changeUsed(false);
            }
        });
    }
}
