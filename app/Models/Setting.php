<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */
    
    protected $fillable = [
        'key',
        'value',
        'value_en',
        'value_ar',
        'type',
    ];

    /**
     * Get setting value by locale.
     *
     * @param  string  $locale
     * @return mixed
     */
    public function getValue($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $field = "value_{$locale}";
        
        $value = $this->$field ?? $this->value_en;

        // Handle boolean type
        if ($this->type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // Handle number type
        if ($this->type === 'number') {
            return is_numeric($value) ? (float) $value : 0;
        }

        return $value;
    }

    /**
     * Scope a query to filter by type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by key.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $key
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Get image URL attribute.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if ($this->type === 'image' && $this->value_en) {
            return asset('storage/' . $this->value_en);
        }
        return null;
    }

    /**
     * Get value attribute (for backward compatibility).
     * Returns value based on current locale.
     *
     * @return mixed
     */
    public function getValueAttribute()
    {
        // Avoid accessor if we're checking raw attributes
        if (array_key_exists('value', $this->attributes)) {
            return $this->attributes['value'];
        }
        return $this->getValue();
    }

    /**
     * Set value attribute mutator.
     * When setting 'value', also set locale-specific fields.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $value;
        // Also set locale-specific values if not already set
        if (!isset($this->attributes['value_en'])) {
            $this->attributes['value_en'] = $value;
        }
        if (!isset($this->attributes['value_ar'])) {
            $this->attributes['value_ar'] = $value;
        }
    }

    /**
     * Static helper to get setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }
}
