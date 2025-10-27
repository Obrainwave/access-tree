<?php

namespace Obrainwave\AccessTree\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'is_root_user' => $this->is_root_user,
            'roles_count' => $this->whenLoaded('roles', fn() => $this->roles->count()),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'permissions' => $this->when($this->relationLoaded('roles'), function () {
                return $this->roles->flatMap->permissions->unique('id');
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
