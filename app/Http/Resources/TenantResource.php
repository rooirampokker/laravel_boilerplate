<?php

namespace app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $controllerAndAction = getControllerNameAndAction($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'tenancy_db_name' => $this->tenancy_db_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            //only include this with the 'show' method
            'domains' => $this->when($controllerAndAction[1] === 'show', $this->domains->pluck('domain')),
        ];
    }
}
