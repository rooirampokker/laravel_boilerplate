<?php

if (!function_exists('getPaginationData')) {
    /**
     * @param $collection
     * @return mixed
     */
    function getPaginationData($collection)
    {
        return [
            'perPage' => $collection->perPage(),
            'currentPage' => $collection->currentPage(),
            'lastPage' => $collection->lastPage(),
            'total' => $collection->total(),
        ];
    }
}

if (!function_exists('getCursorPaginationData')) {
    /**
     * @param $collection
     * @return mixed
     */
    function getCursorPaginationData($collection)
    {

        $nextCursor = $collection->nextCursor();
        $previousCursor = $collection->previousCursor();

        return [
            'perPage' => $collection->perPage(),
            'next_cursor' => $nextCursor ? $nextCursor->encode() : null,
            'next_page_url' => $collection->nextPageUrl(),
            'previous_cursor' => $previousCursor ? $previousCursor->encode() : null,
            'previous_page_url' => $collection->previousPageUrl(),
        ];
    }

    if (!function_exists('paginateCollection')) {
        /**
         * @param $collection
         * @return mixed
         */
        function paginateCollection($collection, $collectionName = 'collection')
        {
            return [
                'pagination' => getPaginationData($collection),
                $collectionName => $collection
            ];
        }
    }

    if (!function_exists('cursorPaginateCollection')) {
        /**
         * @param $collection
         * @return mixed
         */
        function cursorPaginateCollection($collection, $collectionName = 'collection')
        {
            return [
                'cursor_pagination' => getCursorPaginationData($collection),
                $collectionName => $collection
            ];
        }
    }

    if (!function_exists('applyPagination')) {
        function appendPaginationToResponse($collection, $response)
        {
            if (isset($response['pagination'])) {
                $collection['pagination'] = $response['pagination'];
            }

            if (isset($response['cursor_pagination'])) {
                $collection['cursor_pagination'] = $response['cursor_pagination'];
            }

            return $collection;
        }
    }
}
