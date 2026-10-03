<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'domain' => $this->domain,
            'logo_url' => $this->logo_url,
            'primary_color' => $this->primary_color,
            'is_active' => (bool) $this->is_active,
            'settings' => $this->settings,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
