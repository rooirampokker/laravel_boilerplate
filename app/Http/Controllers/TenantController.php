<?php

namespace App\Http\Controllers;

use App\Repository\Eloquent\TenantRepository;
use App\Http\Resources\TenantResource;
use Illuminate\Http\Request;
use App\Http\Requests\TenantStoreRequest;
use App\Http\Requests\TenantUpdateRequest;

class TenantController extends Controller
{
    private TenantRepository $tenantRepository;

    public function __construct(TenantRepository $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }

    /**
     * returns all tenants
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = $this->tenantRepository->index();
        if ($response) {
            $collection = TenantResource::collection($response);

            return response()->json($this->ok(__('tenants.index.success'), $collection));
        }

        $responseMessage = $this->error(__('tenants.index.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $response = $this->tenantRepository->show($id);
        if ($response) {
            $collection = TenantResource::collection([$response]);

            return response()->json($this->ok(__('tenants.show.success'), $collection));
        }

        $responseMessage = $this->error(__('tenants.show.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param TenantStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TenantStoreRequest $request)
    {
        $response = $this->tenantRepository->store($request);
        if ($response) {
            $collection = TenantResource::collection([$response]);
            return response()->json($this->ok(__('tenants.store.success'), $collection));
        }

        $responseMessage = $this->error(__('tenants.store.failed'));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $response = $this->tenantRepository->delete($id);
        if ($response) {
            return response()->json($this->ok(__('tenants.delete.success', ['id' => $id])));
        }

        $responseMessage = $this->error(__('tenants.delete.failed', ['id' => $id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param TenantUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TenantUpdateRequest $request, $id)
    {
        $response = $this->tenantRepository->update($request, $id);
        if ($response) {
            return response()->json($this->ok(__('tenants.update.success', ['id' => $id])));
        }

        $responseMessage = $this->error(__('tenants.update.failed', ['id' => $id]));
        return response()->json($responseMessage, $responseMessage['code']);
    }
}
