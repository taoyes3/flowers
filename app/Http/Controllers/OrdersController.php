<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\OrderService;
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
}
