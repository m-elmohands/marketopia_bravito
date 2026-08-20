<?php

namespace App\Repository\Eloquent;

use App\Models\ShippingReturn;
use App\Repository\Contracts\ShippingReturnRepositoryInterface;

class ShippingReturnRepository implements ShippingReturnRepositoryInterface
{
    public function getAll()
    {
        return ShippingReturn::all();
    }

    public function getActive()
    {
        return ShippingReturn::active()->get();
    }

    public function getOrdered()
    {
        return ShippingReturn::active()->ordered()->get();
    }

    public function getBySlug($slug)
    {
        return ShippingReturn::where('slug', $slug)->firstOrFail();
    }

    public function getById($id)
    {
        return ShippingReturn::findOrFail($id);
    }

    public function create(array $data)
    {
        return ShippingReturn::create($data);
    }

    public function update($id, array $data)
    {
        $item = ShippingReturn::findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        $item = ShippingReturn::findOrFail($id);
        return $item->delete();
    }
}
