<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curriculum\LessonStoreRequest;
use App\Http\Requests\Curriculum\ModuleStoreRequest;
use App\Http\Resources\LessonResource;
use App\Http\Resources\ModuleResource;
use App\Models\Cohort;
use App\Models\CohortEnrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CurriculumController extends Controller
{
    /**
     * Add a module/chapter to a course.
     */
    public function storeModule(ModuleStoreRequest $request): JsonResponse
    {
        $module = Module::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Module created successfully',
            'data' => new ModuleResource($module),
        ], 201);
    }

    /**
     * Add a lesson to a module.
     */
    public function storeLesson(LessonStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $lesson = Lesson::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Lesson created successfully',
            'data' => new LessonResource($lesson),
        ], 201);
    }

    /**
     * Show a single lesson.
     */
    public function showLesson($id): JsonResponse
    {
        $lesson = Lesson::with('module.course')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new LessonResource($lesson),
        ]);
    }

    /**
     * Mark a lesson as completed by the student and recalculate cohort progress.
     */
    public function completeLesson(Request $request, $id): JsonResponse
    {
        $lesson = Lesson::with('module.course')->findOrFail($id);
        $user = Auth::user();

        $cohortId = $request->input('cohort_id');
        if (!$cohortId) {
            // Find active cohort user is enrolled in that contains this lesson's course
            $enrollment = CohortEnrollment::where('user_id', $user->id)
                ->where('status', '!=', 'dropped')
                ->whereHas('cohort.courses', function ($q) use ($lesson) {
                    $q->where('courses.id', $lesson->module->course_id);
                })
                ->first();

            $cohortId = $enrollment?->cohort_id;
        }

        if (!$cohortId) {
            return response()->json([
                'success' => false,
                'message' => 'Student is not enrolled in an active cohort for this course.',
            ], 422);
        }

        // 1. Record Lesson Progress
        $progress = LessonProgress::updateOrCreate(
            [
                'cohort_id' => $cohortId,
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        // 2. Recalculate Overall Cohort Progress Percentage
        $totalLessons = Lesson::whereHas('module', function ($q) use ($lesson) {
            $q->where('course_id', $lesson->module->course_id);
        })->count();

        $completedLessons = LessonProgress::where('cohort_id', $cohortId)
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('lesson.module', function ($q) use ($lesson) {
                $q->where('course_id', $lesson->module->course_id);
            })
            ->count();

        $progressPercentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100, 2)
            : 100.00;

        $enrollment = CohortEnrollment::where('cohort_id', $cohortId)
            ->where('user_id', $user->id)
            ->first();

        if ($enrollment) {
            $enrollment->update([
                'progress_percentage' => $progressPercentage,
                'completed_at' => $progressPercentage >= 100.00 ? now() : null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lesson marked as completed',
            'data' => [
                'lesson_id' => $lesson->id,
                'completed_at' => $progress->completed_at->toIso8601String(),
                'cohort_id' => $cohortId,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'progress_percentage' => $progressPercentage,
            ],
        ]);
    }
}
