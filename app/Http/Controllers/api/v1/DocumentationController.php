<?php

namespace App\Http\Controllers\api\v1;

use App\Repository\DocumentationRepository;
use function App\Http\Controllers\httpStatusCode;

class DocumentationController extends Controller
{
    private DocumentationRepository $documentationRepository;

    public function __construct(DocumentationRepository $documentationRepository)
    {
        $this->documentationRepository = $documentationRepository;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $response = $this->documentationRepository->index();

        if ($response) {
            return $response;
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }
}
