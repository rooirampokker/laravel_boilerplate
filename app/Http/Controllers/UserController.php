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

        return response()->json($response);
    }

    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAll()
    {
        $response = $this->userRepository->indexAll();

        return response()->json($response);
    }
    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexTrashed()
    {
        $response = $this->userRepository->indexTrashed();

        return response()->json($response);
    }
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $response = $this->userRepository->show($id);

        return response()->json($response);

    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $response = $this->userRepository->login($request);

        return response()->json($response);
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
            return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $response = $this->userRepository->update($request, $id);

        if (!$response) {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }

        return response()->json(['success' => __('general.record.update.success', ['id' => $id])], httpStatusCode('SUCCESS'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $response = $this->userRepository->delete($id);

        return response()->json($response);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        $response = $this->userRepository->restore($id);

        return response()->json($response);
    }
}
