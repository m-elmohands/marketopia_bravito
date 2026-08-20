<?php

namespace App\Repository\Contracts;

interface SettingRepositoryInterface
{
    public function getAll();
    
    public function get($key, $locale = null);
    
    public function set($key, $value_en, $value_ar = null, $type = 'text');
    
    public function getByType($type);
    
    public function getById($id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($key);
    
    public function deleteById($id);
    
    // Legacy methods for backward compatibility
    public function getDeliverymanValue();
    
    public function getValue(string $key);
}
