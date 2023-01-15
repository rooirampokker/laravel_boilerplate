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

            return response()->json($this->ok(__('roles.index.success'), $roleCollection));
        }

        $responseMessage = $this->error(__('roles.index.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $response = $this->roleRepository->show($id);

        if ($response) {
            $roleCollection = RoleResource::collection([$response]);

            return response()->json($this->ok(__('roles.show.success'), $roleCollection));
        }

        $responseMessage = $this->error(__('roles.show.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }
    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $response = $this->roleRepository->store($request);

        if ($response) {
            $userCollection = RoleResource::collection($response);
            return response()->json($this->ok(__('roles.store.success'), $userCollection));
        }

        $responseMessage = $this->error(__('roles.store.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $response = $this->roleRepository->update($request, $id);

        return response()->json($response, $response['code']);
    }
}
