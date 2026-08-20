<?php

namespace App\Repository\Contracts;

interface NavigationMenuRepositoryInterface
{
    public function getAll();
    public function getActive();
    public function getOrdered();
    public function getByLocation($location);
    public function getRootItems();
    public function getMenuTree($location);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
