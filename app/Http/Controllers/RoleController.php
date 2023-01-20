<?php

namespace App\Http\Controllers;

use App\Repository\Eloquent\RoleRepository;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;

class RoleController extends Controller
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = $this->roleRepository->index();
        if ($response) {
            $collection = RoleResource::collection($response);

            return response()->json($this->ok(__('roles.index.success'), $collection));
        }

        $responseMessage = $this->error(__('roles.index.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $response = $this->roleRepository->show($id);
        if ($response) {
            $collection = RoleResource::collection([$response]);

            return response()->json($this->ok(__('roles.show.success'), $collection));
        }

        $responseMessage = $this->error(__('roles.show.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param RoleStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleStoreRequest $request)
    {
        $response = $this->roleRepository->store($request);
        if ($response) {
            $collection = RoleResource::collection([$response]);
            return response()->json($this->ok(__('roles.store.success'), $collection));
        }

        $responseMessage = $this->error(__('roles.store.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $response = $this->roleRepository->delete($id);
        if ($response) {
            return response()->json($this->ok(__('roles.delete.success', ['id' => $id])));
        }

        $responseMessage = $this->error(__('roles.delete.failed', ['id' => $id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param RoleUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        $response = $this->roleRepository->update($request, $id);
        if ($response) {
            return response()->json($this->ok(__('roles.update.success', ['id' => $id])));
        }

        $responseMessage = $this->error(__('roles.update.failed', ['id' => $id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermission(Request $request, $id)
    {
        $response = $this->roleRepository->addPermission($request, $id);
        $permissions = implode(',', $request->get('permissions'));
        if ($response) {
            $collection = RoleResource::collection([$response]);

            return response()->json($this->ok(__('roles.permissions.create.success', ['role_id' => $id, 'permission_id' => $permissions]), $collection));
        }

        $responseMessage = $this->error(__('roles.permissions.create.failed',  ['role_id' => $id, 'permission_id' => $permissions]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $role_id
     * @param $permission_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokePermission($role_id, $permission_id)
    {
        $response = $this->roleRepository->revokePermission($role_id, $permission_id);
        if ($response) {
            $collection = RoleResource::collection([$response]);

            return response()->json($this->ok(__('roles.permissions.delete.success', ['role_id' => $role_id, 'permission_id' => $permission_id]), $collection));
        }

        $responseMessage = $this->error(__('roles.permissions.delete.failed',  ['role_id' => $role_id, 'permission_id' => $permission_id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPermission(Request $request, $id)
    {
        $response = $this->roleRepository->syncPermission($request, $id);
        $permissions = implode(',', $request->get('permissions'));
        if ($response) {
            $collection = RoleResource::collection([$response]);

            return response()->json($this->ok(__('roles.permissions.sync.success', ['role_id' => $id, 'permission_id' => $permissions]), $collection));
        }

        $responseMessage = $this->error(__('roles.permissions.sync.failed',  ['role_id' => $id, 'permission_id' => $permissions]));
        return response()->json($responseMessage, $responseMessage['code']);
    }
}
