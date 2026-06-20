<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class TenantAccess
{
    public function handle(Request $request, Closure $next, $tenantId)
    {
        $user = auth()->user();
        if ($user->role === 'super_admin') return $next($request);
        if ($user->tenant_id !== $tenantId) abort(403, 'Sem acesso a este negócio');
        return $next($request);
    }
}
