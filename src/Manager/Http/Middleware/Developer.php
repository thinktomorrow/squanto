<?php

namespace Thinktomorrow\Squanto\Manager\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class Developer
{
    /**
     * Restrict access for developers only.
     *
     * This is a very simplistic and modest approach to shielding off these routes
     * so for your production projects you should setup your own permissions logic instead.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $loggedUser = Auth::guard($guard)->user();

        if (!$loggedUser || ! $loggedUser->isSquantoDeveloper()) {
            return redirect('/');
        }

        return $next($request);
    }
}
