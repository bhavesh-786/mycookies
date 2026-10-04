<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'total_price',
        'selected_addons',
    ];

    protected function casts(): array
    {
        return [
            'selected_addons' => 'array',
            'addons' => 'array',
        ];
    }
}
