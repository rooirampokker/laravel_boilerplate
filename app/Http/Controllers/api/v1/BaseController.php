<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Routing\Controller;

use App\Traits\ResponseTrait;
use App\Traits\ControllerTrait;

class BaseController extends Controller
{
    use ResponseTrait;
    use ControllerTrait;


    protected $request;
    protected $model;
    protected $modelName;
    protected $modelPath;
    protected $repositoryPath;
    protected $repository;
    protected $language;
    protected $apiVersion;

    public function __construct($modelName = '')
    {
        $this->apiVersion = '\api\\v1\\';
        $this->modelName = $modelName;
        $this->request = request()->query();
        $this->modelPath = 'App\\Models\\' . $modelName;
        $this->repositoryPath = "App\Http\Repository" . $this->apiVersion . $modelName . "Repository";
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $response = $this->repository->index($this->request);
        $collection = $this->formatCollectionRelations($response, $this->request, new $this->model());
        if (!empty($collection)) {
            return response()->json(
                $this->ok(
                    __(
                        $this->language . '.index.success'
                    ),
                    $collection
                )
            );
        }

        $responseMessage = $this->notFound(__(
            $this->language . '.index.failed'
        ));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $response = $this->repository->show($id);
        $collection = $this->formatCollectionRelations($response, $this->request, new $this->model());

        if (!empty($collection)) {
            return response()->json($this->ok(__(
                $this->language . '.show.success'
            ), $collection));
        }

        $responseMessage = $this->notFound(__(
            $this->language . '.show.failed'
        ));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $response = $this->repository->delete($id);

        if (!empty($response)) {
            return response()->json($this->ok(__(
                $this->language . '.delete.success',
                ['id' => $id]
            )));
        }

        $responseMessage = $this->notFound(__(
            $this->language . '.delete.failed',
            ['id' => $id]
        ));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $response = $this->repository->restore($id);

        if (!empty($response)) {
            return response()->json($this->ok(__($this->language . '.restore.success', ['id' => $id])));
        }

        $responseMessage = $this->notFound(__(
            $this->language . '.restore.failed',
            ['id' => $id]
        ));
        return response()->json($responseMessage, $responseMessage['code']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function clone($id)
    {
        $response = $this->repository->clone($id);

        if (!empty($response)) {
            return response()->json($this->ok(__(
                $this->language . '.clone.success',
                ['id' => $id]
            ), $response));
        }

        $responseMessage = $this->notFound(__(
            $this->language . '.clone.failed',
            ['id' => $id]
        ));
        return response()->json($responseMessage, $responseMessage['code']);
    }
}
