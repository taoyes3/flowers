<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

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

        $grid->disableActions();  // 禁用操作列

        return $grid;
    }
}
