<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
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
            'client_name' => $this->client_name,
            'client_position' => $this->client_position,
            'client_company' => $this->client_company,
            'client_image_url' => $this->client_image_url,
            'rating' => $this->rating,
            'testimonial' => $this->testimonial,
            'is_featured' => $this->is_featured,
            'order' => $this->order,
        ];
    }
}
