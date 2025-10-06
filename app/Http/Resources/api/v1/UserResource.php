<?php

namespace App\Http\Resources\api\v1;

class UserResource extends BaseResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];

        $relations['data'] = $this->router->allowedactions(['show', 'store', 'update'], 'data') ? $this->hydrateData($request, $this->data) : null;
        $relations['roles'] = $this->router->allowedActions(['store'], 'roles') ? $this->setResource($this, 'roles') : null;

        return $this->composeResourceReturnArray($coreModel, $relations);
    }
}
