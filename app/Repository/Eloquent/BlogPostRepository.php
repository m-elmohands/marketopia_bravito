<?php

namespace App\Repository\Eloquent;

use App\Models\BlogPost;
use App\Repository\Contracts\BlogPostRepositoryInterface;

class BlogPostRepository implements BlogPostRepositoryInterface
{
    public function getAll()
    {
        return BlogPost::with(['category', 'author'])->get();
    }

    public function getActive()
    {
        return BlogPost::active()->with(['category', 'author'])->get();
    }

    public function getPublished()
    {
        return BlogPost::published()->with(['category', 'author'])->latest('published_at');
    }

    public function getFeatured()
    {
        return BlogPost::published()->featured()->with(['category', 'author'])->limit(6)->get();
    }

    public function getByCategory($categoryId)
    {
        return BlogPost::published()->byCategory($categoryId)->with(['category', 'author'])->get();
    }

    public function getBySlug($slug)
    {
        return BlogPost::where('slug', $slug)->with(['category', 'author'])->firstOrFail();
    }

    public function incrementViews($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->increment('views_count');
        return $post;
    }

    public function create(array $data)
    {
        return BlogPost::create($data);
    }

    public function update($id, array $data)
    {
        $post = BlogPost::findOrFail($id);
        $post->update($data);
        return $post;
    }

    public function delete($id)
    {
        $post = BlogPost::findOrFail($id);
        return $post->delete();
    }
}
