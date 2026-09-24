<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AddonGroup extends Model
{
    protected $guarded = [];

    public function options(): HasMany
    {
        return $this->hasMany(AddonOption::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return (app()->getLocale() === 'ar' && !empty($this->name_ar))
            ? $this->name_ar
            : $this->name;
    }
}
