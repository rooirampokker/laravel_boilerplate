<?php

if (!function_exists('getExceptionType')) {
    /**
     * @param \Exception $e
     * @return mixed
     */
    function getExceptionType(\Exception $e)
    {
        if (strpos(get_class($e), "AuthorizationException")) {
            $httpStatus =  httpStatusCode('FORBIDDEN');
        } elseif (strpos(get_class($e), "ModelNotFoundException")) {
            $httpStatus = httpStatusCode('NOT_FOUND');
        } else {
            $httpStatus = httpStatusCode('NOT_IMPLEMENTED');
        }
        return $httpStatus;
    }
}

if (!function_exists('httpStatusCode')) {
    /**
     * Syntactic sugar lookup array - maps http response codes up to a more descriptive label.
     *
     * @param $statusLabel
     * @return mixed
     */
    function httpStatusCode($statusLabel)
    {
        $statusMapping = [
            'SUCCESS' => 200,
            'CREATED' => 201,
            'NO_CONTENT' => 204,
            'BAD_REQUEST' => 400,
            'UNAUTHORISED' => 401,
            'FORBIDDEN' => 403,
            'NOT_FOUND'  => 404,
            'NOT_IMPLEMENTED' => 501
        ];

        return $statusMapping[strtoupper($statusLabel)];
    }
}

/**
 * Entity-Attribute-Value parser.
 * Assumes the injected collection has a data relationship defined with key and value attributes
 *
 * @param $collection
 * @return array
 */
if (!function_exists('eavParser'))
{
    function eavParser($collection): array
    {
        $dataCollection = [];
        foreach ($collection->data as $data) {
            $dataCollection['data'][$data->key] = $data->value;
        }

        return array_merge($collection->toArray(), $dataCollection);
    }
}
