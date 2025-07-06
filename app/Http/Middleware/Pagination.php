<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Pagination
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $limit = $request->query('limit');
        if ($limit) {
            $upperLimit = config('view.pagination_upper_limit');
            $limit = min($limit, $upperLimit); // Cap the limit at 100
            $request->merge(['limit' => $limit]); // Override the 'limit' parameter in the request
        }
        return $next($request);
    }
}
