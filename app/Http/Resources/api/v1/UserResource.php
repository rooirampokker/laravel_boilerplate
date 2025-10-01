<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Role;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = [
            'id' => $this->id,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'data' => $this->data,
            'roles' => [],
        ];

        if (count($this->roles)) {
            //roles returns as collection when assigning new roles, but as array otherwise
            $roles = is_array($this->roles) ? Role::hydrate($this->roles) : $this->roles;
            $user['roles'] = RoleResource::collection($roles);
        }

        return $user;
    }
}
