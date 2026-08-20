<?php

namespace App\Repository\Contracts;

interface AboutSectionRepositoryInterface
{
    public function getAll();
    public function getActive();
    public function getOrdered();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
