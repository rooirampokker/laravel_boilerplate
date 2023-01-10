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

        return response()->json($response, $response['code']);
    }

    /**
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function find($token)
    {
        $response = $this->passwordResetRepository->find($token);

        return response()->json($response, $response['code']);
    }

    /**
     * @param  Request $request
     * @return mixed
     */
    public function reset(Request $request)
    {
        $response = $this->passwordResetRepository->reset($request);

        return response()->json($response, $response['code']);
    }
}
