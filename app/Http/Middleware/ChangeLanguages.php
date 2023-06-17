<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Auth;

class ChangeLanguages
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
        config(['app.locale' => Session::get('language', config('app.locale'))]);
        return $next($request);
    }
}
