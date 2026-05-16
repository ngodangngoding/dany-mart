<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'expense_category',
        'description',
        'amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
