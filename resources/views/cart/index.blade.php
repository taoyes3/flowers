@extends('layouts.app')

@section('title', '购物车')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">我的购物车</div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>商品信息</th>
                            <th>单价</th>
                            <th>数量</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody class="product_list">
                        @foreach($cartItems as $cartItem)
                            <tr data-id="{{ $cartItem->productSku->id }}">
                                <td>
                                    <input type="checkbox" name="select" value="{{ $cartItem->product_sku_id }}" {{ $cartItem->productSku->product->on_sale ? 'checked' : 'disabled' }}>
                                </td>
                                <td class="product_info">
                                    <div class="preview">
                                        <a href="{{ route('products.show', ['product' => $cartItem->productSku->product_id]) }}" target="_blank" >
                                            <img src="{{ $cartItem->productSku->product->image_url }}">
                                        </a>
                                    </div>
                                    <div @if(!$cartItem->productSku->product->on_sale) class="not_on_sale" @endif>
                                        <span class="product_title">
                                            <a href="{{ route('products.show', ['product' => $cartItem->productSku->product_id]) }}">
                                                {{ $cartItem->productSku->product->title }}
                                            </a>
                                        </span>
                                        <span class="sku_title">{{ $cartItem->productSku->title }}</span>
                                        @if(!$cartItem->productSku->product->on_sale)
                                            <span class="warning">该商品已下架</span>
                                        @endif
                                    </div>
                                </td>
                                <td><span class="price">￥{{ $cartItem->productSku->price }}</span></td>
                                <td>
                                    <input type="text" name="amount" value="{{ $cartItem->amount }}" class="form-control form-control-sm amount" @if(!$cartItem->productSku->product->on_sale) disabled @endif>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-danger btn-remove">移除</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        <form class="form-horizontal" role="form" id="order-form">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3 text-md-right">选择收货地址</label>
                                <div class="col-sm-9 col-md-7">
                                    <select name="address" class="form-control">
                                        @foreach($addresses as $address)
                                            <option value="{{ $address->id }}">{{ $address->full_address }} {{ $address->contact_name }} {{ $address->contact_phone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-3 text-md-right">备注</label>
                                <div class="col-sm-9 col-md-7">
                                    <textarea name="remark" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="offset-sm-3 col-sm-3">
                                    <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scriptsAfterJs')
    <script>
        $(function () {
            // 移除按钮点击事件
            $('.btn-remove').click(function () {
                var id = $(this).closest('tr').data('id');
                swal({
                    title: '确认要将该商品移除？',
                    icon: 'warning',
                    buttons: ['取消', '确定'],
                    dangerMode: true,
                })
                    .then(function (willDelete) {
                        if (!willDelete) {
                            return;
                        }
                        axios.delete('/cart/' + id)
                            .then(function () {
                                location.reload();
                            });
                    });
            });

            $('#select-all').change(function () {
                var checked = $(this).prop('checked');
                $('input[name=select][type=checkbox]:not([disabled])').each(function () {
                    $(this).prop('checked', checked);
                });
            });

            // 监听订单创建按钮事件
            $('.btn-create-order').click(function () {
                // 构建请求参数，将用户选择的地址id和备注内容写入请求参数
                var req = {
                    address_id: $('#order-form').find('select[name=address]').val(),
                    items: [],
                    remark: $('#order-form').find('textarea[name=remark]').val(),
                };

                // 遍历 <table> 标签内所有带有 data-id 属性的 <tr> 标签，也就是每个购物车中商品的 SKU
                $('table tr[data-id]').each(function () {
                    // 获取当前行的复选框
                    var $checkbox = $(this).find('input[name=select][type=checkbox]');

                    // 如果复选框被禁用或者为选中则跳过
                    if ($checkbox.prop('disabled') || !$checkbox.prop('checked')) {
                        return;
                    }

                    // 获取当前商品数量
                    var $input = $(this).find('input[name=amount]');

                    // 检测数量是否为0或非合法，若是则跳过
                    if ($input.val() == 0 || isNaN($input.val())) {
                        return;
                    }

                    // 把 SKU id 和数量存入请求参数数组中
                    req.items.push({
                        sku_id: $(this).data('id'),
                        amount: $input.val(),
                    });
                });
                // console.log(req);return;
                axios.post('{{ route('orders.store') }}', req)
                    .then(function (response) {
                        // console.log(response);return;
                        swal('订单提交成功', '', 'success');
                    }, function (error) {
                        // console.log(error);return;
                        if (error.response.status === 422) {
                            // http 状态码为 422 代表用户输入校验失败
                            var html = '<div>';
                            _.each(error.response.data.errors, function (errors) {
                                _.each(errors, function (error) {
                                    html += error+'<br>';
                                })
                            });
                            html += '</div>';
                            swal({content: $(html)[0], icon: 'error'})
                        } else {
                            // 其他情况应该是系统挂了
                            swal('系统错误', '', 'error');
                        }
                    });
            });
        });
    </script>
@endsection