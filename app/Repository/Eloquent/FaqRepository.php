<?php

namespace App\Repository\Eloquent;

use App\Models\Faq;
use App\Repository\Contracts\FaqRepositoryInterface;

class FaqRepository implements FaqRepositoryInterface
{
    public function getAll()
    {
        return Faq::all();
    }

    public function getActive()
    {
        return Faq::active()->get();
    }

    public function getOrdered()
    {
        return Faq::active()->ordered()->get();
    }

    public function getByCategory($category)
    {
        return Faq::active()->byCategory($category)->ordered()->get();
    }

    public function getCategories()
    {
        return Faq::active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();
    }

    public function getById($id)
    {
        return Faq::findOrFail($id);
    }

    public function create(array $data)
    {
        return Faq::create($data);
    }

    public function update($id, array $data)
    {
        $faq = Faq::findOrFail($id);
        $faq->update($data);
        return $faq;
    }

    public function delete($id)
    {
        $faq = Faq::findOrFail($id);
        return $faq->delete();
    }
}
