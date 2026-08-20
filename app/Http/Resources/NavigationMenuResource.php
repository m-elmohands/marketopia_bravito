<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NavigationMenuResource extends JsonResource
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
            'url' => $this->url,
            'route_name' => $this->route_name,
            'target' => $this->target,
            'icon' => $this->icon,
            'order' => $this->order,
            'location' => $this->location,
            'children' => NavigationMenuResource::collection($this->whenLoaded('children')),
        ];
    }
}
