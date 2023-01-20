<?php

namespace App\Http\Controllers;

use App\Repository\Eloquent\UserRepository;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use Validator;

class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $response = $this->userRepository->login($request);

        return response()->json($response, $response['code']);
    }
    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = $this->userRepository->index();
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
    public function indexAll()
    {
        $response = $this->userRepository->indexAll();
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
        $response = $this->userRepository->indexTrashed();
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.index.success'), $collection));
        }

        $responseMessage = $this->error(__('users.index.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $response = $this->userRepository->show($id);
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.show.success'), $collection));
        }

        $responseMessage = $this->error(__('users.show.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStoreRequest $request)
    {
        $response = $this->userRepository->store($request);
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.store.success'), $collection));
        }

        $responseMessage = $this->error(__('users.store.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $response = $this->userRepository->update($request, $id);
        if ($response) {
            return response()->json($this->ok(__('users.update.success', ['id' => $id])));
        }

        $responseMessage = $this->error(__('users.update.failed', ['id' => $id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $response = $this->userRepository->delete($id);

        if ($response) {
            return response()->json($this->ok(__('users.delete.success', ['id' => $id])));
        }

        $responseMessage = $this->error(__('users.delete.failed', ['id' => $id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        $response = $this->userRepository->restore($id);

        if ($response) {
            return response()->json($this->ok(__('users.restore.success', ['id' => $id])));
        }

        $responseMessage = $this->error(__('users.restore.failed', ['id' => $id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }
    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncRole(Request $request, $id)
    {
        $response = $this->userRepository->syncRole($request, $id);
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
        $response = $this->userRepository->addRole($request, $id);
        $roles = implode(',', $request->get('roles'));
        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.roles.create.success', ['user_id' => $id, 'role_id' => $roles]), $collection));
        }

        $responseMessage = $this->error(__('users.roles.create.failed',  ['user_id' => $id, 'role_id' => $roles]));
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
        $response = $this->userRepository->removeRole($user_id, $role_id);

        if ($response) {
            $collection = UserResource::collection($response);

            return response()->json($this->ok(__('users.roles.remove.success', ['user_id' => $user_id, 'role_id' => $role_id]), $collection));
        }

        $responseMessage = $this->error(__('users.roles.remove.failed', ['user_id' => $user_id, 'role_id' => $role_id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }
}
