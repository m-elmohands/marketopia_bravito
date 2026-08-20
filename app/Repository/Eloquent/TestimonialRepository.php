<?php

namespace App\Repository\Eloquent;

use App\Models\Testimonial;
use App\Repository\Contracts\TestimonialRepositoryInterface;

class TestimonialRepository implements TestimonialRepositoryInterface
{
    public function getAll()
    {
        return Testimonial::all();
    }

    public function getActive()
    {
        return Testimonial::active()->get();
    }

    public function getFeatured()
    {
        return Testimonial::active()->featured()->ordered()->get();
    }

    public function getOrdered()
    {
        return Testimonial::active()->ordered()->get();
    }

    public function getByRating($rating)
    {
        return Testimonial::active()->byRating($rating)->ordered()->get();
    }

    public function getById($id)
    {
        return Testimonial::findOrFail($id);
    }

    public function create(array $data)
    {
        return Testimonial::create($data);
    }

    public function update($id, array $data)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update($data);
        return $testimonial;
    }

    public function delete($id)
    {
        $testimonial = Testimonial::findOrFail($id);
        return $testimonial->delete();
    }
}
