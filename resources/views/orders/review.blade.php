@extends('layouts.app')

@section('title', '商品评价')

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header">
                    商品评价
                    <a href="{{ route('orders.index') }}" class="float-right">返回订单列表</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.review.store', [$order]) }}" method="post">
                        {{ csrf_field() }}
                        <table class="table">
                            <tbody>
                            <tr>
                                <td>商品名称</td>
                                <td>打分</td>
                                <td>评价</td>
                            </tr>
                            @foreach($order->items as $index => $item)
                                <tr>
                                    <td class="product-info">
                                        <div class="preview">
                                            <a href="{{ route('products.show', [$item->product_id]) }}" target="_blank">
                                                <img src="{{ $item->product->image_url }}">
                                            </a>
                                        </div>
                                        <div>
                                            <span class="product-title">
                                                <a href="{{ route('products.show', [$item->product_id]) }}" target="_blank">{{ $item->product->title }}</a>
                                            </span>
                                            <span class="sku-title">{{ $item->productSku->title }}</span>
                                        </div>
                                        <input type="hidden" name="reviews[{{$index}}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="vertical-middle">
                                        @if($order->reviewed)
                                            <span class="rating-star-yes">{{ str_repeat('★', $item->rating) }}</span><span class="rating-star-no">{{ str_repeat('★', 5 - $item->rating) }}</span>
                                        @else
                                            <ul class="rate-area">
                                                <input type="radio" name="reviews[{{$index}}][rating]" value="5" id="5-star-{{$index}}" checked><label for="5-star-{{$index}}"></label>
                                                <input type="radio" name="reviews[{{$index}}][rating]" value="4" id="4-star-{{$index}}"><label for="4-star-{{$index}}"></label>
                                                <input type="radio" name="reviews[{{$index}}][rating]" value="3" id="3-star-{{$index}}"><label for="3-star-{{$index}}"></label>
                                                <input type="radio" name="reviews[{{$index}}][rating]" value="2" id="2-star-{{$index}}"><label for="2-star-{{$index}}"></label>
                                                <input type="radio" name="reviews[{{$index}}][rating]" value="1" id="1-star-{{$index}}"><label for="1-star-{{$index}}"></label>
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->reviewed)
                                            {{ $item->review }}
                                        @else
                                            <textarea class="form-control {{ $errors->has('reviews.' . $index . '.review') ? 'is-invalid' : '' }}" name="reviews[{{$index}}][review]"></textarea>
                                            @if($errors->has('reviews.' . $index . '.review'))
                                                @foreach($errors->get('reviews.' . $index . '.review') as $msg)
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $msg }}</strong></span>
                                                @endforeach
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-center">
                                    @if($order->reviewed)
                                        <a href="{{ route('orders.show', [$order]) }}" class="btn btn-primary">查看订单</a>
                                    @else
                                        <button type="submit" class="btn btn-primary center-block">提交</button>
                                    @endif
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection