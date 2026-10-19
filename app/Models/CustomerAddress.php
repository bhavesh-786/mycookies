<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customer_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'type',                  // Home, Office, Friend
        'area_id',
        'area_name',
        'block',
        'street',
        'avenue',
        'building',
        'floor',
        'flat_no',
        'additional_directions',
        'paci',
        'latitude',
        'longitude',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default'  => 'boolean',
        'latitude'    => 'float',
        'longitude'   => 'float',
        'area_id'     => 'integer',
        'customer_id' => 'integer',
    ];

    /**
     * Get the customer that owns the address.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Optional relationship to the Area model.
     */
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    /**
     * Accessor for full formatted Kuwait address text.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->area_name) {
            $parts[] = $this->area_name;
        }

        $parts[] = 'Block ' . $this->block;
        $parts[] = 'Street ' . $this->street;

        if ($this->avenue) {
            $parts[] = 'Avenue ' . $this->avenue;
        }

        $parts[] = 'Building ' . $this->building;

        if ($this->floor) {
            $parts[] = 'Floor ' . $this->floor;
        }

        if ($this->flat_no) {
            $parts[] = 'Flat/Office ' . $this->flat_no;
        }

        if ($this->paci) {
            $parts[] = 'PACI: ' . $this->paci;
        }

        return implode(', ', $parts);
    }
}
