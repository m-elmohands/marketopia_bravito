<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute()
    {
        $locale = app()->getLocale() ?? 'en';
        return $this->{'name_' . $locale} ?? $this->name_en;
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values')
            ->withPivot('id', 'attribute_value_id', 'product_id');
    }
}
