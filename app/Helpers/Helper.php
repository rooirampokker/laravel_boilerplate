<?php

if (!function_exists('getExceptionType')) {
    /**
     * @param  \Exception $e
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

/*
 * syntactic sugar lookup array - maps http response codes up to a more descriptive label.
 */
if (!function_exists('httpStatusCode')) {
    /**
     * @param  $statusLabel
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
