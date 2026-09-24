<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'order_number',
        'user_id',
        'order_type',
        'store_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'area_name',
        'area_id',
        'address_type',
        'block',
        'street',
        'building',
        'building_or_house',
        'avenue',
        'paci',
        'paci_number',
        'latitude',
        'longitude',
        'payment_method',
        'subtotal',
        'delivery_fee',
        'total',
        'status',
        'special_remarks',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
