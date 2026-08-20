<?php

namespace App\Repository\Contracts;

interface BlogPostRepositoryInterface
{
    public function getAll();
    public function getActive();
    public function getPublished();
    public function getFeatured();
    public function getByCategory($categoryId);
    public function getBySlug($slug);
    public function incrementViews($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
