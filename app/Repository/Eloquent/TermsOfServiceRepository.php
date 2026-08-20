<?php

namespace App\Repository\Eloquent;

use App\Models\TermsOfService;
use App\Repository\Contracts\TermsOfServiceRepositoryInterface;

class TermsOfServiceRepository implements TermsOfServiceRepositoryInterface
{
    public function getAll()
    {
        return TermsOfService::all();
    }

    public function getActive()
    {
        return TermsOfService::active()->get();
    }

    public function getOrdered()
    {
        return TermsOfService::active()->ordered()->get();
    }

    public function getBySlug($slug)
    {
        return TermsOfService::where('slug', $slug)->firstOrFail();
    }

    public function getById($id)
    {
        return TermsOfService::findOrFail($id);
    }

    public function create(array $data)
    {
        return TermsOfService::create($data);
    }

    public function update($id, array $data)
    {
        $item = TermsOfService::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        $item = TermsOfService::findOrFail($id);
        return $item->delete();
    }
}
