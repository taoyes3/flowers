<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        // $products = Product::query()->where('on_sale', true)->paginate(16);

        // 创建一个查询构造器
        $builder = Product::query()->where('on_sale', true);

        // search 参数用来模糊搜索商品
        if ($search = $request->input('search', '')) {
            $like = '%' . $search . '%';
            // 模糊搜索商品标题、商品描述、sku标题、sku描述
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                       $query->where('title', 'like', $like)
                           ->orWhere('description', 'like', $like);
                    });
            });
        }

        // order 参数用来控制商品的排序规则
        if ($order = $request->input('order')) {
            // 是否以asc或者desc结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // 如果字符串开头是这三个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate(16);

        return view('products.index', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'order' => $order,
            ],
        ]);
    }

    public function show(Product $product, Request $request)
    {
        // dd($product->skus()->where('id', 3)->first()->product);
        // 判断商品是否已经上架，如果没有上架则抛出异常
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        $favored = false;

        if ($user = $request->user()) {
            // boolval() 函数用于把值转为布尔值
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        return view('products.show', ['product' => $product, 'favored' => $favored]);
    }

    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }

    public function favorites(Request $request)
    {
        $user = $request->user();
        $products = $user->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }
}
