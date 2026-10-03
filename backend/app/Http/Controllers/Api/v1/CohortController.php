<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cohort\CohortStoreRequest;
use App\Http\Resources\CohortResource;
use App\Models\Cohort;
use App\Models\CohortEnrollment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CohortController extends Controller
{
    /**
     * List all cohorts for the active tenant.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Cohort::query()
            ->with(['courses.modules'])
            ->latest('start_date');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $cohorts = $query->paginate($request->integer('per_page', 15));

        return CohortResource::collection($cohorts);
    }

    /**
     * Create a new cohort.
     */
    public function store(CohortStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if (!isset($data['capacity'])) {
            $data['capacity'] = 500; // Approved default capacity
        }

        $cohort = Cohort::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Cohort created successfully',
            'data' => new CohortResource($cohort),
        ], 201);
    }

    /**
     * Show a single cohort with curriculum details.
     */
    public function show($id): JsonResponse
    {
        $cohort = Cohort::with(['courses.modules.lessons'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new CohortResource($cohort),
        ]);
    }

    /**
     * Update cohort settings or status.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $cohort = Cohort::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'capacity' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', 'string', 'in:upcoming,active,completed,archived'],
            'settings' => ['sometimes', 'array'],
        ]);

        $cohort->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cohort updated successfully',
            'data' => new CohortResource($cohort),
        ]);
    }

    /**
     * Enroll a student into a cohort with 500-capacity validation.
     */
    public function enroll(Request $request, $id): JsonResponse
    {
        $cohort = Cohort::findOrFail($id);

        // Target user: current user or specified user if admin
        $targetUserId = Auth::id();
        if ($request->filled('user_id') && (Auth::user()->can('cohorts.enroll') || Auth::user()->hasRole(['super_admin', 'tenant_admin']))) {
            $targetUserId = $request->input('user_id');
        }

        // 1. Check Capacity Limit (500)
        if ($cohort->isFull()) {
            return response()->json([
                'success' => false,
                'message' => "Cohort has reached its maximum capacity of {$cohort->capacity} learners.",
            ], 422);
        }

        // 2. Check if already enrolled
        $existing = CohortEnrollment::where('cohort_id', $cohort->id)
            ->where('user_id', $targetUserId)
            ->first();

        if ($existing) {
            if ($existing->status === 'dropped') {
                $existing->update(['status' => 'active', 'enrolled_at' => now()]);
                return response()->json([
                    'success' => true,
                    'message' => 'Enrollment reactivated successfully',
                    'data' => new CohortResource($cohort->fresh()),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'User is already enrolled in this cohort.',
            ], 422);
        }

        // 3. Create Enrollment
        CohortEnrollment::create([
            'cohort_id' => $cohort->id,
            'user_id' => $targetUserId,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0.00,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in cohort',
            'data' => new CohortResource($cohort->fresh()),
        ], 201);
    }

    /**
     * Get all cohorts enrolled or instructed by the authenticated user.
     */
    public function myCohorts(Request $request): AnonymousResourceCollection
    {
        $user = Auth::user();

        if ($user->hasRole(['super_admin', 'tenant_admin'])) {
            $cohorts = Cohort::with(['courses.modules'])->latest('start_date')->get();
        } elseif ($user->hasRole('instructor')) {
            $cohorts = Cohort::whereHas('courses', function ($q) use ($user) {
                $q->where('instructor_id', $user->id);
            })->with(['courses.modules'])->latest('start_date')->get();
        } else {
            // Learner
            $cohorts = $user->cohorts()->with(['courses.modules'])->latest('start_date')->get();
        }

        return CohortResource::collection($cohorts);
    }
}
