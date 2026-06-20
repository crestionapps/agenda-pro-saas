<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class Authenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            if ($request->expectsJson()) return response()->json(['error' => 'Unauthenticated'], 401);
            return redirect()->route('login');
        }
        return $next($request);
    }
}
