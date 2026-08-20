<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'image',
        'popular',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->select(
            'id',
            app()->getLocale() == 'ar' ? 'name_ar as name' : 'name_en as name',
            'slug',
            'image',
            'popular',
            'parent_id',
        );
    }

    public function getNameAttribute($value)
    {
        if (! is_null($value)) {
            return $value;
        }

        $locale = app()->getLocale();
        return $this->attributes["name_{$locale}"] ?? $this->attributes['name_en'] ?? null;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
