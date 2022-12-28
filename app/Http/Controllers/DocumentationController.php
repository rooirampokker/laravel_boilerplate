<?php

namespace App\Http\Controllers;

use App\Repository\Eloquent\DocumentationRepository;

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
            return response()->json(['success' => $response], httpStatusCode('SUCCESS'));
        } else {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
    }

}
