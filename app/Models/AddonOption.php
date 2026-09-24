<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddonOption extends Model
{
    protected $guarded = [];

    public function getDisplayNameAttribute(): string
    {
        return (app()->getLocale() === 'ar' && !empty($this->name_ar))
            ? $this->name_ar
            : $this->name;
    }
}
