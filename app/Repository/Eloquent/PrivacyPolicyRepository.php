<?php

namespace App\Repository\Eloquent;

use App\Models\PrivacyPolicy;
use App\Repository\Contracts\PrivacyPolicyRepositoryInterface;

class PrivacyPolicyRepository implements PrivacyPolicyRepositoryInterface
{
    public function getAll()
    {
        return PrivacyPolicy::all();
    }

    public function getActive()
    {
        return PrivacyPolicy::active()->get();
    }

    public function getOrdered()
    {
        return PrivacyPolicy::active()->ordered()->get();
    }

    public function getBySlug($slug)
    {
        return PrivacyPolicy::where('slug', $slug)->firstOrFail();
    }

    public function getById($id)
    {
        return PrivacyPolicy::findOrFail($id);
    }

    public function create(array $data)
    {
        return PrivacyPolicy::create($data);
    }

    public function update($id, array $data)
    {
        $item = PrivacyPolicy::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        $item = PrivacyPolicy::findOrFail($id);
        return $item->delete();
    }
}
