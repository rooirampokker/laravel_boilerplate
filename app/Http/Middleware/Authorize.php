<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Middleware\App;
use App\Traits\ResponseTrait;

class Authorize
{
    use ResponseTrait;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $controller = $request->route()->action["uses"];
        $model = getModelNameFromRoute($request);
        $controllerAndMethod = preg_split("/[@]/", $controller);

        if (empty($controllerAndMethod)) {
            $response = $this->unauthorised(__('auth.unauthorized'));
            return response()->json($response, $response['code']);
        }
        $user = $request->user();
        //dd($controllerAndMethod[1]);
        if (!empty($user) && $user->can($controllerAndMethod[1], app($model))) {
            return $next($request);
        }

        $response = $this->unauthorised(__('auth.unauthorized'));
        return response()->json($response, $response['code']);
    }
}
