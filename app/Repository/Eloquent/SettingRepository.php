<?php

namespace App\Repository\Eloquent;

use App\Models\Setting;
use App\Repository\Contracts\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    protected $model;

    public function __construct(Setting $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function get($key, $locale = null)
    {
        $setting = $this->model->byKey($key)->first();
        
        if (!$setting) {
            return null;
        }

        return $setting->getValue($locale);
    }

    public function set($key, $value_en, $value_ar = null, $type = 'text')
    {
        return $this->model->updateOrCreate(
            ['key' => $key],
            [
                'value_en' => $value_en,
                'value_ar' => $value_ar ?? $value_en,
                'type' => $type,
            ]
        );
    }

    public function getByType($type)
    {
        return $this->model->byType($type)->get();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $setting = $this->getById($id);
        $setting->update($data);
        return $setting;
    }

    public function delete($key)
    {
        return $this->model->where('key', $key)->delete();
    }

    public function deleteById($id)
    {
        $setting = $this->getById($id);
        return $setting->delete();
    }

    // Legacy methods for backward compatibility
    public function getDeliverymanValue()
    {
        $setting = $this->model->where('key', 'deliveryman')->first();
        return $setting ? $setting->value_en : null;
    }

    public function getValue(string $key)
    {
        return $this->get($key);
    }
}
