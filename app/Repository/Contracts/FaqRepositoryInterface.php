<?php

namespace App\Repository\Contracts;

interface FaqRepositoryInterface
{
    public function getAll();
    public function getActive();
    public function getOrdered();
    public function getByCategory($category);
    public function getCategories();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
