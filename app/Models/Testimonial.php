<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name_en',
        'client_name_ar',
        'client_position_en',
        'client_position_ar',
        'client_company_en',
        'client_company_ar',
        'client_image',
        'testimonial_en',
        'testimonial_ar',
        'rating',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function getClientImageUrlAttribute()
    {
        return $this->client_image ? Storage::url($this->client_image) : null;
    }

    public function getClientNameAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"client_name_{$locale}"} ?? $this->client_name_en;
    }

    public function getClientPositionAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"client_position_{$locale}"} ?? $this->client_position_en;
    }

    public function getClientCompanyAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"client_company_{$locale}"} ?? $this->client_company_en;
    }

    public function getTestimonialAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"testimonial_{$locale}"} ?? $this->testimonial_en;
    }
}
