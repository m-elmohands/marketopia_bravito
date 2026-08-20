<?php

namespace App\Repository\Eloquent;

use App\Models\AboutSection;
use App\Repository\Contracts\AboutSectionRepositoryInterface;

class AboutSectionRepository implements AboutSectionRepositoryInterface
{
    public function getAll()
    {
        return AboutSection::all();
    }

    public function getActive()
    {
        return AboutSection::active()->get();
    }

    public function getOrdered()
    {
        return AboutSection::active()->ordered()->get();
    }

    public function getById($id)
    {
        return AboutSection::findOrFail($id);
    }

    public function create(array $data)
    {
        return AboutSection::create($data);
    }

    public function update($id, array $data)
    {
        $section = $this->getById($id);
        $section->update($data);
        return $section;
    }

    public function delete($id)
    {
        $section = $this->getById($id);
        return $section->delete();
    }
}
