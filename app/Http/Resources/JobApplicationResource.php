<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'job_post_id' => $this->job_post_id,
            'job_post' => new JobPostResource($this->whenLoaded('jobPost')),
            'user' => new UserResource($this->whenLoaded('user')),
            'cv' => new CvResource($this->whenLoaded('cv')),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'cover_letter' => $this->cover_letter,
            'status' => $this->status,
            'notes' => $this->when($this->notes !== null, $this->notes),
            'applied_at' => $this->applied_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
