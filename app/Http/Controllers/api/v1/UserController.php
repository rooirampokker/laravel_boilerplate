<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\api\v1\UserResource;
use Illuminate\Http\Request;


class UserController extends BaseController
{

    public function __construct()
    {
        parent::__construct('User');
        $this->setModelAndRepository();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $response = $this->repository->login($request);

        return response()->json($response, $response['code']);
    }

    /**
     * @param UserStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        $response = $this->repository->store($request);
        return $this->processStoreResponse($response);
    }

    /**
     * @param UserUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $response = $this->repository->update($request, $id);
        return $this->processUpdateResponse($response);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncRole(Request $request, $id)
    {
        $response = $this->repository->syncRole($request, $id);
        $roles = implode(',', $request->get('roles'));
        if (!empty($response)) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__(
                $this->language . '.roles.sync.success',
                ['user_id' => $id, 'role_id' => $roles]
            ), $collection));
        }

        return response()->json($this->error(__(
            $this->language . '.roles.sync.failed',
            ['user_id' => $id, 'role_id' => $roles]
        )));
    }
    
    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addRole(Request $request, $id)
    {
        $response = $this->repository->addRole($request, $id);
        $roles = implode(',', $request->get('roles'));

        if (!empty($response)) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__(
                $this->language . '.roles.create.success',
                ['user_id' => $id, 'role_id' => $roles]
            ), $collection));
        }

        return response()->json($this->error(__(
            $this->language . '.roles.create.failed',
            ['user_id' => $id, 'role_id' => $roles]
        )));
    }

    /**
     * Removes a single role from a user, based on parameter keys
     *
     * @param $userId
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeRole($userId, $roleId)
    {
        $response = $this->repository->removeRole($userId, $roleId);

        if (!empty($response)) {
            $collection = UserResource::collection($response);
            return response()->json($this->ok(__(
                $this->language . '.roles.remove.success',
                ['user_id' => $userId, 'role_id' => $roleId]
            ), $collection));
        }

        return response()->json($this->error(__(
            $this->language . '.roles.remove.failed',
            ['user_id' => $userId, 'role_id' => $roleId]
        )));
    }
}
