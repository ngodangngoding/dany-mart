<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'code'
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->code)) {
                $category->code = self::getCategoryCode($category->name);
            }
        });
    }

    public static function getCategoryCode($name)
    {
        $uppercaseName = strtoupper($name);
        $cleanName = str_replace(' ', '', $uppercaseName);

        $firstTwoChars = substr($cleanName, 0, 2);

        $thirdChar = substr($cleanName, 2, 1);
        if (!$thirdChar) {
            $thirdChar = 'A';
        }

        $finalCode = $firstTwoChars . $thirdChar;

        $isCodeExists = self::where('code', $finalCode)->exists();

        if ($isCodeExists) {
            $finalCode = $firstTwoChars . 'X';
        }

        return $finalCode;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
