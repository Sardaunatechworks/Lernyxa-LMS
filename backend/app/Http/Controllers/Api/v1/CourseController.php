<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\CourseStoreRequest;
use App\Http\Resources\CourseResource;
use App\Models\Cohort;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * List all courses for the active tenant.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Course::query()
            ->with(['modules.lessons', 'creator'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        return CourseResource::collection($query->paginate($request->integer('per_page', 15)));
    }

    /**
     * Create a new course.
     */
    public function store(CourseStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        $data['created_by'] = Auth::id();

        $course = Course::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => new CourseResource($course),
        ], 201);
    }

    /**
     * Show a course with curriculum hierarchy.
     */
    public function show($id): JsonResponse
    {
        $course = Course::with(['modules.lessons', 'creator'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new CourseResource($course),
        ]);
    }

    /**
     * Update course details.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'thumbnail_url' => ['nullable', 'url'],
            'difficulty_level' => ['sometimes', 'string', 'in:beginner,intermediate,advanced'],
            'status' => ['sometimes', 'string', 'in:draft,published,archived'],
        ]);

        $course->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => new CourseResource($course),
        ]);
    }

    /**
     * Associate a course to a cohort.
     */
    public function attachToCohort(Request $request, $id): JsonResponse
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'cohort_id' => ['required', 'exists:cohorts,id'],
            'instructor_id' => ['nullable', 'exists:users,id'],
            'order_index' => ['sometimes', 'integer', 'min:1'],
        ]);

        $cohort = Cohort::findOrFail($validated['cohort_id']);

        $cohort->courses()->syncWithoutDetaching([
            $course->id => [
                'instructor_id' => $validated['instructor_id'] ?? Auth::id(),
                'order_index' => $validated['order_index'] ?? 1,
                'is_active' => true,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course successfully linked to cohort',
        ]);
    }
}
