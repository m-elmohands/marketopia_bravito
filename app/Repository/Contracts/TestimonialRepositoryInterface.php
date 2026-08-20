<?php

namespace App\Repository\Contracts;

interface TestimonialRepositoryInterface
{
    public function getAll();
    public function getActive();
    public function getFeatured();
    public function getOrdered();
    public function getByRating($rating);
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
