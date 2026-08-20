<?php

namespace App\Repository\Eloquent;

use App\Models\Service;
use App\Repository\Contracts\ServiceRepositoryInterface;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function getAll()
    {
        return Service::all();
    }

    public function getActive()
    {
        return Service::active()->get();
    }

    public function getOrdered()
    {
        return Service::active()->ordered()->get();
    }

    public function getBySlug($slug)
    {
        return Service::where('slug', $slug)->firstOrFail();
    }

    public function getById($id)
    {
        return Service::findOrFail($id);
    }

    public function create(array $data)
    {
        return Service::create($data);
    }

    public function update($id, array $data)
    {
        $service = Service::findOrFail($id);
        $service->update($data);
        return $service;
    }

    public function delete($id)
    {
        $service = Service::findOrFail($id);
        return $service->delete();
    }
}
