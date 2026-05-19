<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'name',
        'unit',
        'purchase_price',
        'selling_price',
        'stock'
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->code)) {
                $product->code = self::getProductCode($product->category_id);
            }
        });
    }

    public static function getProductCode($categoryId)
    {
        $category = Category::find($categoryId);
        $prefix = $category->code;

        $lastProduct = self::where('category_id', $categoryId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastProduct) {
            $sequenceNumber = 1;
        } else {
            $codeParts = explode('-', $lastProduct->code);
            $lastNumber = (int) end($codeParts);
            $sequenceNumber = $lastNumber + 1;
        }

        $formattedNumber = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);

        return $prefix . '-' . $formattedNumber;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(ProductStockHistory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}