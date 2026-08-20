<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'featured_image_url' => $this->featured_image_url,
            'category' => $this->when($this->category, [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'author' => $this->when($this->author, [
                'id' => $this->author?->id,
                'name' => $this->author?->name,
            ]),
            'published_at' => $this->published_at?->toDateTimeString(),
            'is_featured' => $this->is_featured,
            'views_count' => $this->views_count,
        ];
    }
}
