<?php

namespace App\Http\Resources\api\v1;

class RoleResource extends BaseResource
{
    public function __construct($resource)
    {
        parent::__construct($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $coreModel = [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        $relations['permissions'] = $this->router->allowedActions([], 'permissions') ? $this->setResource($this, 'permissions') : null;
        $relations['users'] = $this->router->allowedActions([], 'users') ? $this->setResource($this, 'users') : null;

        return $this->composeResourceReturnArray($coreModel, $relations);
    }

}
