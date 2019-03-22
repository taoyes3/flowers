<?php

namespace App\Admin\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('订单列表')
            ->body($this->grid());
    }

    public function show(Order $order, Content $content)
    {
        return $content
            ->header('查看订单')
            // body 方法可以接受 Laravel 的视图作为参数
            ->body(view('admin.orders.show', ['order' => $order->load(['items.product', 'items.productSku'])]));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        // 只展示已支付订单，默认支付时间倒序
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        $grid->no('订单流水号');

        // 展示关联关系
        $grid->column('user.name', '买家');

        $grid->total_amount('总金额')->sortable();
        $grid->paid_at('支付时间')->sortable();

        $grid->ship_status('物流')->display(function ($value) {
            return Order::$shipStatusMap[$value];
        });

        $grid->refund_status('退款状态')->display(function ($value) {
            return Order::$refundStatusMap[$value];
        });

        $grid->created_at('创建时间');

        $grid->disableCreateButton();  // 禁用创建按钮

        $grid->tools(function ($tools) {
            $tools->batch(function ($batch) {
                $batch->disableDelete();  // 禁用批量删除按钮
            });
        });

        // $grid->disableActions();  // 禁用操作列

        $grid->actions(function ($actions) {
            $actions->disableDelete();  // 禁用删除按钮
            $actions->disableEdit();  // 禁用编辑按钮
        });

        return $grid;
    }

    /**
     * send out goods
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ship(Order $order, Request $request)
    {
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未付款');
        }

        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已发货');
        }

        // Laravel 5.5 之后，validate 方法返回校验后的数据
        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no' => ['required'],
        ], [], [
            'express_company' => '物流公司',
            'express_no' => '物流单号',
        ]);

        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data' => $data, // 在 Order 模型 $cast 属性里已经指明 ship_data 是一个数组，因此这里可以直接把数组传过去
        ]);

        return redirect()->back(); // 返回上一页
    }
}
