<?php

namespace App\Http\Controllers;

use App\Repository\Eloquent\UserRepository;
use Illuminate\Http\Request;
use App\Http\Resources\UserCollection;
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

        return response()->json($response, $response['code']);
    }

    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAll()
    {
        $response = $this->userRepository->indexAll();

        return response()->json($response, $response['code']);
    }
    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexTrashed()
    {
        $response = $this->userRepository->indexTrashed();

        return response()->json($response, $response['code']);
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
        $response = $this->userRepository->store($request);

        return response()->json($response, $response['code']);
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
}
