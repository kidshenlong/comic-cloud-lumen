<?php

namespace App\Http\Middleware;

/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 06/10/15
 * Time: 01:15
 */

use Closure;

class HeaderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->isJson() && $request->method() != "GET") abort(400); //TODO: Correct error message and also check if get requests should require content type headers.
        return $next($request);
    }
}
