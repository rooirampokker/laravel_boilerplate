<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class DocumentationService
{
    private string $docPath;

    public function __construct()
    {
        $this->docPath = resource_path('yml/');
    }

    /**
     * Fetches YML header and 'footer', combines it and returns as basic YML structure
     * @return string
     */
    public function prepareDocStructure()
    {
        $header          = File::get($this->docPath . 'header.yml');
        $containerSchema = File::get($this->docPath . 'schema.container.yml');

        return $header . $containerSchema;
    }

    /**
     * Fetches individual schemas and endpoints, consolidates it and return as array for final token replacement and merge
     * @param $docStructure
     * @return array
     */
    function combineDocElements($docStructure)
    {
        $endpoints = File::glob($this->docPath . '/*/' . 'endpoints.*.yml');
        $schemas   = File::glob($this->docPath . '/schemas/schema.*.yml');
        $consolidatedSchemas = $consolidatedEndpoints = '';

        if (count($schemas)) {
            foreach ($schemas as $schema) {
                $consolidatedSchemas .= File::get($schema);
            }
        }
        if (count($endpoints)) {
            foreach ($endpoints as $endpoint) {
                $consolidatedEndpoints .= File::get($endpoint);
            }
        }

        return ["docStructure" => $docStructure,
            "endpoints"    => $consolidatedEndpoints,
            "schemas"      => $consolidatedSchemas];
    }

    /**
     * Token replacement is used to inject consolidated endpoints and schemas into basic YML structure
     * @param $combinedDoc
     * @return array|string|string[]
     */
    function doTokenReplacement($combinedDoc)
    {
        $apiDoc = str_replace(
            "ENDPOINTS_INJECTED_HERE",
            $combinedDoc["endpoints"],
            $combinedDoc["docStructure"]
        );
        $apiDoc = str_replace(
            "SCHEMAS_INJECTED_HERE",
            $combinedDoc["schemas"],
            $apiDoc
        );

        return str_replace(
            "APP_URL",
            config('app.url') . "/api",
            $apiDoc
        );
    }
}
