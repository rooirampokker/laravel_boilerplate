<?php

namespace App\Http\Controllers;

use App\Repository\Eloquent\RoleRepository;
use App\Http\Resources\RoleResource;

class RoleController extends Controller
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
    /**
     * returns all active/non-deleted users
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = $this->roleRepository->index();

        if ($response) {
            $roleCollection = RoleResource::collection($response);

            return response()->json($this->ok(__('users.index.success'), $roleCollection));
        }

        $responseMessage = $this->error(__('users.index.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }
}
