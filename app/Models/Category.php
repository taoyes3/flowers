<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'is_directory', 'level', 'path'];

    protected $casts = [
        'is_directory' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Category $category) {
            // 是否为根目录
            if (is_null($category->parent_id)) {
                $category->level = 0;
                $category->path = '-';
            } else {
                $category->level = $category->parent->level + 1;
                $category->path = $category->parent->path . $category->parent_id . '-';
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getPathIdsAttribute()
    {
        // 获取所有祖先类目的 ID 值
        return array_filter(explode('-', trim($this->path, '-')));
    }

    public function getAncestorsAttribute()
    {
        // 获取所有祖先类目并按层级排序
        return Category::query()->whereIn('id', $this->path_ids)->orderBy('level')->get();
    }

    public function getFullNameAttribute()
    {
        // 获取以 - 为分隔符的所有祖先类目名称以及当前类目的名称
        return $this->ancestors->pluck('name')->push($this->name)->implode('-');
    }
}
