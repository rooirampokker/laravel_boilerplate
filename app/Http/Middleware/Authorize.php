<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authorize
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $controller = $request->route()->action["uses"];
        $controllerAndMethod = preg_split("/[@]/", $controller);

        if (empty($controllerAndMethod)) {
            return $this->unauthorised(__('users.permissions.unauthorised'));
        }

        $user = $request->user();
        if (!empty($user) && $user->can($controllerAndMethod[1], $controllerAndMethod[0])) {
            return $next($request);
        }

        return $this->unauthorised(__('users.permissions.unauthorised'));
    }
}
