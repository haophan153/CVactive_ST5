<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CvResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'template' => new TemplateResource($this->whenLoaded('template')),
            'personal_info' => $this->personal_info,
            'objective' => $this->objective,
            'theme_color' => $this->theme_color,
            'font_family' => $this->font_family,
            'visibility' => $this->visibility,
            'is_draft' => $this->is_draft,
            'sections' => CvSectionResource::collection($this->whenLoaded('sections')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
