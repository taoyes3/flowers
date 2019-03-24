<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = $user->orders()->with(['items.product', 'items.productSku'])->orderBy('created_at', 'desc')->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = userAddress::find($request->input('address_id'));
        $remark = $request->input('remark');
        $items = $request->input('items');

        $order = $orderService->store($user, $address, $remark, $items);

        return $order;
    }

    public function show(Order $order)
    {
        $this->authorize('own', $order);
        $items = $order->items()->with(['product', 'productSku'])->get();
        // dd($items);
        return view('orders.show', ['order' => $order, 'items' => $items]);
    }

    /**
     * receive goods
     * @param Order $order
     * @return Order
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function received(Order $order)
    {
        $this->authorize('own', $order);

        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        $order->update([
            'ship_status' => Order::SHIP_STATUS_RECEIVED,
        ]);

        return $order;
    }

    /**
     * review page
     * @param Order $order
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function review(Order $order)
    {
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        return view('orders.review', ['order' => $order->load(['items.product', 'items.productSku'])]);
    }

    /**
     * send review
     * @param Order $order
     * @param SendReviewRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }

        $reviews = $request->input('reviews');

        // 开启事物
        \DB::transaction(function () use ($reviews, $order) {

            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 订单明细
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }

            // 将订单标记为已评价
            $order->update(['reviewed' => true]);

            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }
}
