<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'title'                => $this->title,
            'description'          => $this->description,
            'location'             => $this->location,
            'job_type'            => $this->job_type,
            'type_info'            => $this->type_info,
            'category'             => $this->category,
            'category_info'        => $this->category_info,
            'experience_level'     => $this->experience_level,
            'experience_info'      => $this->experience_info,
            'salary_min'           => $this->salary_min,
            'salary_max'           => $this->salary_max,
            'salary_label'        => $this->salary_label,
            'salary_range'        => $this->salary_range,
            'salary_currency'     => $this->salary_currency,
            'company_name'         => $this->company_name,
            'company_description'  => $this->company_description,
            'company_logo'         => $this->company_logo_url,
            'company_initials'     => $this->company_initials,
            'contact_email'       => $this->contact_email,
            'contact_phone'       => $this->contact_phone,
            'status'              => $this->status,
            'is_remote'           => $this->is_remote,
            'is_hot'              => $this->is_hot ?? false,
            'is_new'              => $this->is_new,
            'is_urgent'           => $this->is_urgent,
            'views_count'         => $this->views_count ?? 0,
            'views_label'         => $this->views_label,
            'user'                => new UserResource($this->whenLoaded('user')),
            'applications_count'   => $this->when($this->applications_count !== null, fn() => (int) $this->applications_count),
            'published_at'         => $this->published_at?->toIso8601String(),
            'published_for_humans'=> $this->published_for_humans,
            'expires_at'           => $this->expires_at?->toIso8601String(),
            'days_until_expiry'   => $this->days_until_expiry,
            'created_at'          => $this->created_at->toIso8601String(),
            'share_url'           => $this->share_url,
        ];
    }
}
