<?php

namespace App\Scopes;

use App\Services\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // If logged-in user is super_admin, do not filter by tenant unless specifically requested
        if (Auth::check() && Auth::user()->hasRole('super_admin')) {
            return;
        }

        $tenantContext = app(TenantContext::class);
        $tenantId = $tenantContext->getTenantId();

        // If not set in context, check if authenticated user belongs to a tenant
        if (!$tenantId && Auth::check() && Auth::user()->tenant_id) {
            $tenantId = Auth::user()->tenant_id;
        }

        if ($tenantId) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        }
    }
}
