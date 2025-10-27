<?php

namespace Obrainwave\AccessTree\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'roles_count' => $this->whenLoaded('roles', fn() => $this->roles->count()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
