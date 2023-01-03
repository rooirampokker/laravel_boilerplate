<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\Eloquent\PasswordResetRepository;

class PasswordResetController extends Controller
{
    private PasswordResetRepository $passwordResetRepository;

    public function __construct(PasswordResetRepository $passwordResetRepository)
    {
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $response = $this->passwordResetRepository->create($request);

        if (!$response) {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
        return response()->json(['success' => __('validation.reset_token.success')], httpStatusCode('SUCCESS'));
    }

    /**
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function find($token)
    {
        $response = $this->passwordResetRepository->find($token);

        if (!$response) {
            return response()->json(['error' => __('validation.reset_token.invalid')], httpStatusCode('401'));
        }
        return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function reset(Request $request)
    {
        $response = $this->passwordResetRepository->reset($request);

        if (!$response) {
            return response()->json(['error' => __('validation.reset_token.invalid')], httpStatusCode(BAD_REQUEST));
        }
        return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
    }
}
