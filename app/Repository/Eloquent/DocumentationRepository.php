<?php

namespace App\Repository\Eloquent;

use App\Services\DocumentationControllerService;
use App\Repository\DocumentationRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DocumentationRepository extends BaseRepository implements DocumentationRepositoryInterface
{
    private DocumentationControllerService $documentationControllerService;
    private DocumentationRepository $documentationRepository;

    public function __construct()
    {
        $this->documentationControllerService = new DocumentationControllerService();
    }


    /**
     * @return array
     */
    public function index() {
        try {

        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $exception->getTrace());
            throw $exception;
        }

        return true;
    }
}
