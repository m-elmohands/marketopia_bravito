<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->getValue($locale),
            'type' => $this->type,
            'image_url' => $this->when($this->type === 'image', $this->image_url),
        ];
    }
}
