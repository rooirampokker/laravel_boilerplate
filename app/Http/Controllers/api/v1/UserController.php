<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\api\v1\UserResource;
use Illuminate\Http\Request;
use Validator;

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
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAll()
    {
        $response = $this->repository->indexAll();
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.index.success'), $collection));
        }

        $responseMessage = $this->error(__('users.index.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }
    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexTrashed()
    {
        $response = $this->repository->indexTrashed();
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.index.success'), $collection));
        }

        $responseMessage = $this->error(__('users.index.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param UserStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        $response = $this->repository->store($request);
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.store.success'), $collection));
        }

        $responseMessage = $this->error(__('users.store.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param UserUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $response = $this->repository->update($request, $id);
        if ($response) {
            return response()->json($this->ok(__('users.update.success', ['id' => $id])));
        }

        $responseMessage = $this->error(__('users.update.failed', ['id' => $id]));
        return response()->json($responseMessage, $responseMessage['code']);
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
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.roles.sync.success', ['user_id' => $id, 'role_id' => $roles]), $collection));
        }

        $responseMessage = $this->error(__('users.roles.sync.failed', ['user_id' => $id, 'role_id' => $roles]));
        return response()->json($responseMessage, $responseMessage['code']);
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
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.roles.create.success', ['user_id' => $id, 'role_id' => $roles]), $collection));
        }

        $responseMessage = $this->error(__('users.roles.create.failed', ['user_id' => $id, 'role_id' => $roles]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * Removes a single role from a user, based on parameter keys
     *
     * @param $user_id
     * @param $role_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeRole($user_id, $role_id)
    {
        $response = $this->repository->removeRole($user_id, $role_id);

        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.roles.remove.success', ['user_id' => $user_id, 'role_id' => $role_id]), $collection));
        }

        $responseMessage = $this->error(__('users.roles.remove.failed', ['user_id' => $user_id, 'role_id' => $role_id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }
}
