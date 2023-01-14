<?php

namespace App\Http\Controllers;

use App\Repository\Eloquent\UserRepository;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Validator;

class UserController extends Controller
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = $this->userRepository->index();
        if ($response) {
            $userCollection = UserResource::collection($response);

            return response()->json($this->ok(__('users.index.success'), $userCollection));
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
            $userCollection = UserResource::collection($response);

            return response()->json($this->ok(__('users.index.success'), $userCollection));
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
            $userCollection = UserResource::collection($response);

            return response()->json($this->ok(__('users.index.success'), $userCollection));
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
            $userCollection = UserResource::collection($response);

            return response()->json($this->ok(__('users.show.success'), $userCollection));
        }

        $responseMessage = $this->error(__('users.show.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
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
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $response = $this->userRepository->store($request);

        if ($response) {
            $userCollection = UserResource::collection($response);
            return response()->json($this->ok(__('users.store.success'), $userCollection));
        }

        $responseMessage = $this->error(__('users.store.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $response = $this->userRepository->update($request, $id);

        return response()->json($response, $response['code']);
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
            $userCollection = UserResource::collection([$response]);

            return response()->json($this->ok(__('users.roles.sync.success', ['user_id' => $id, 'role_id' => $roles]), $userCollection));
        }

        $responseMessage = $this->error(__('users.roles.sync.failed'), ['user_id' => $id, 'role_id' => $roles]);
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
            $userCollection = UserResource::collection([$response]);

            return response()->json($this->ok(__('users.roles.create.success', ['user_id' => $id, 'role_id' => $roles]), $userCollection));
        }

        $responseMessage = $this->error(__('users.roles.create.failed'), ['user_id' => $id, 'role_id' => $roles]);
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
            $userCollection = UserResource::collection([$response]);

            return response()->json($this->ok(__('users.roles.remove.success', ['user_id' => $user_id, 'role_id' => $role_id]), $userCollection));
        }

        $responseMessage = $this->error(__('users.roles.remove.failed', ['user_id' => $user_id, 'role_id' => $role_id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }
}
