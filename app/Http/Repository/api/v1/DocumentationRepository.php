<?php

namespace App\Http\Repository\api\v1;

use App\Http\Repository\api\v1\Interfaces\DocumentationRepositoryInterface;
use App\Services\DocumentationService;
use Illuminate\Support\Facades\Log;

class DocumentationRepository extends BaseRepository implements DocumentationRepositoryInterface
{
    private DocumentationService $service;
    //private DocumentationRepository $documentationRepository;

    public function __construct()
    {
        $this->service = new DocumentationService();
    }


    /**
     * @return array
     */
    public function index()
    {
        try {
            $docStructure = $this->service->prepareDocStructure();
            $combinedDoc  = $this->service->combineDocElements($docStructure);

            return $this->service->doTokenReplacement($combinedDoc);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return false;
    }
}
