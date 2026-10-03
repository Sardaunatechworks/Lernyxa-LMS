<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate user and issue session / token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember', false);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is ' . $user->status . '. Please contact your administrator.',
            ], 403);
        }

        // Authenticate session if web request
        if ($request->hasSession()) {
            Auth::login($user, $remember);
            $request->session()->regenerate();
        }

        $user->update(['last_login_at' => now()]);

        // Create API token for stateless / cross-domain clients
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Register a new learner.
     */
    public function register(RegisterRequest $request, TenantContext $tenantContext): JsonResponse
    {
        $validated = $request->validated();

        // Resolve Tenant
        $tenantId = null;
        if (!empty($validated['tenant_id'])) {
            $tenantId = $validated['tenant_id'];
        } elseif (!empty($validated['tenant_slug'])) {
            $tenantId = Tenant::where('slug', $validated['tenant_slug'])->value('id');
        } elseif ($tenantContext->hasTenant()) {
            $tenantId = $tenantContext->getTenantId();
        } else {
            // Default to first active tenant if single-tenant or default tenant configured
            $tenantId = Tenant::where('is_active', true)->value('id');
        }

        $fullName = trim(($validated['first_name'] ?? '') . ' ' . ($validated['last_name'] ?? ''));

        $user = User::create([
            'tenant_id' => $tenantId,
            'name' => $fullName ?: ($validated['name'] ?? 'Learner'),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            'last_login_at' => now(),
        ]);

        // Assign learner role
        $user->assignRole('learner');

        if ($request->hasSession()) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Log the user out (Invalidate token and session).
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        // Delete current access token if present
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        // Invalidate session if session-based
        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
}
