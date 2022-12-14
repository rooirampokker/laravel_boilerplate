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
     * @return mixed
     */
    public function index()
    {
        $response = $this->userRepository->index();

        if ($response) {
            return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $response = $this->userRepository->show($id);

        if ($response) {
            return response()->json(['success' => [$response]], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $response = $this->userRepository->login($request);

        if ($response) {
            return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
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

        if ($response) {
            return response()->json(['success' => __('general.record.destroy.success', ['id' => $id])], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restore($id)
    {
        $response = $this->userRepository->restore($id);

        if ($response) {
            return response()->json(['success' => __('general.record.restore.success', ['id' => $id])], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }
}
