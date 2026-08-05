<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
   {
      if (auth()->user()->role !== $role) {
          abort(403, 'Unauthorized');
      }

      return $next($request);
          
   }
}
