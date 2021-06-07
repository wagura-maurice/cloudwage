<?php

namespace App\Http\Middleware;

use Closure;
use Payroll\Repositories\OrganizationsRepository;

class Connection
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
        if (\Auth::check()) {
            OrganizationsRepository::makeConfig(\Auth::user()->database);
        }

        return $next($request);
    }
}
