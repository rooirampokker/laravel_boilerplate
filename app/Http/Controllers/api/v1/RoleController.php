<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Repository\api\v1\RoleRepository;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\api\v1\RoleResource;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    private RoleRepository $roleRepository;

    public function __construct()
    {
        parent::__construct('Role');
        $this->setModelAndRepository();
    }

    /**
     * @param RoleStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleStoreRequest $request)
    {
        $response = $this->repository->store($request);
        return $this->processStoreResponse($response);
    }

    /**
     * @param UserUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        $response = $this->repository->update($request, $id);
        return $this->processUpdateResponse($response);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermission(Request $request, $id)
    {
        $response = $this->repository->addPermission($request, $id);
        $permissions = implode(',', $request->get('permissions'));
        if ($response) {
            $collection = RoleResource::collection([$response]);

            return response()->json($this->ok(__('roles.permissions.create.success', ['role_id' => $id, 'permission_id' => $permissions]), $collection));
        }

        $responseMessage = $this->error(__('roles.permissions.create.failed', ['role_id' => $id, 'permission_id' => $permissions]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $role_id
     * @param $permission_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokePermission($role_id, $permission_id)
    {
        $response = $this->repository->revokePermission($role_id, $permission_id);
        if ($response) {
            $collection = RoleResource::collection($response);

            return response()->json($this->ok(__('roles.permissions.delete.success', ['role_id' => $role_id, 'permission_id' => $permission_id]), $collection));
        }
        $responseMessage = $this->error(__('roles.permissions.delete.failed', ['role_id' => $role_id, 'permission_id' => $permission_id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPermission(Request $request, $id)
    {
        $response = $this->repository->syncPermission($request, $id);

        $permissions = implode(',', $request->get('permissions'));
        if (!empty($response)) {
            $collection = RoleResource::collection($response);
            return response()->json($this->ok(__(
                $this->language . '.permissions.sync.success',
                ['role_id' => $id, 'permission_id' => $permissions]
            ), $collection));
        }

        $responseMessage = $this->invalidRequest(__(
            $this->language . '.permissions.sync.failed',
            ['role_id' => $id, 'permission_id' => $permissions]
        ));
        return response()->json($responseMessage, $responseMessage['code']);
    }
}
