<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (auth()->check()) return redirect('/');
        return $next($request);
    }
}
