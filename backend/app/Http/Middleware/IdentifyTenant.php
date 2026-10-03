<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function __construct(protected TenantContext $tenantContext)
    {
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = null;

        // 1. Check X-Tenant-ID or X-Tenant-Slug header
        $tenantId = $request->header('X-Tenant-ID');
        $tenantSlug = $request->header('X-Tenant-Slug');

        if ($tenantId) {
            $tenant = Tenant::where('is_active', true)->find($tenantId);
        } elseif ($tenantSlug) {
            $tenant = Tenant::where('is_active', true)->where('slug', $tenantSlug)->first();
        }

        // 2. Check authenticated user's tenant if not resolved by header
        if (!$tenant && Auth::check() && Auth::user()->tenant_id) {
            $tenant = Tenant::where('is_active', true)->find(Auth::user()->tenant_id);
        }

        // 3. Fallback: Host domain/subdomain check
        if (!$tenant) {
            $host = $request->getHost();
            $tenant = Tenant::where('is_active', true)
                ->where(function ($query) use ($host) {
                    $query->where('domain', $host)
                        ->orWhere('slug', explode('.', $host)[0]);
                })->first();
        }

        if ($tenant) {
            $this->tenantContext->setTenant($tenant);
        }

        return $next($request);
    }
}
