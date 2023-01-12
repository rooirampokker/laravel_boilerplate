<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        return response()->json($this->error(__('users.index.failed')));
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

        return response()->json($this->error(__('users.index.failed')));
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

        return response()->json($this->error(__('users.index.failed')));
    }
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $response = $this->userRepository->show($id);

        return response()->json($response, $response['code']);

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
        $rawResponse = $this->userRepository->store($request);
        if ($rawResponse['code'] == 200) {
            $response = new UserResource($rawResponse['data']);
        }

        return response()->json($response, $rawResponse['code']);
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

        return response()->json($response, $response['code']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        $response = $this->userRepository->restore($id);

        return response()->json($response, $response['code']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function addRole($id)
    {
        $response = $this->userRepository->removeRole($id);

        return response()->json($response, $response['code']);
    }
    /**
     * @param $id
     * @return mixed
     */
    public function removeRole($id)
    {
        $response = $this->userRepository->removeRole($id);

        return response()->json($response, $response['code']);
    }
}
