<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = [];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function addonGroups(): HasMany
    {
        return $this->hasMany(AddonGroup::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return (app()->getLocale() === 'ar' && !empty($this->name_ar))
            ? $this->name_ar
            : $this->name;
    }

    public function getDisplayDescriptionAttribute(): ?string
    {
        return (app()->getLocale() === 'ar' && !empty($this->description_ar))
            ? $this->description_ar
            : $this->description;
    }
}
