<?php

namespace App\Repository\Contracts;

interface ContactMessageRepositoryInterface
{
    public function getAll();
    
    public function getToday();
    
    public function getBetweenDates($startDate, $endDate);
    
    public function getByEmail($email);
    
    public function getById($id);
    
    public function create(array $data);
    
    public function delete($id);
    
    public function getLatest($limit = 10);
}
