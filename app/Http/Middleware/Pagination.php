<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Pagination
{
    /**
     * Forces an upper-limit to total results returned via pagination
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $limit = $request->query('limit');
        if ($limit) {
            $upperLimit = config('view.pagination_upper_limit', 100);
            $limit = min($limit, $upperLimit); // Cap the limit at 100
            $request->merge(['limit' => $limit]); // Override the 'limit' parameter in the request
        }
        return $next($request);
    }
}
