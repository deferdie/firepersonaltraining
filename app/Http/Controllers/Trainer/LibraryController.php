<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\LibraryAssessment;
use App\Models\LibraryDocument;
use App\Models\LibraryExercise;
use App\Models\LibraryForm;
use App\Models\LibraryHabit;
use App\Models\LibraryMealPlan;
use App\Models\LibraryVideo;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LibraryController extends Controller
{
    public function index(Request $request): Response
    {
        $trainerId = auth()->id();

        $counts = [
            'programs' => Program::forTrainer($trainerId)->count(),
            'exercises' => LibraryExercise::forTrainer($trainerId)->count(),
            'forms' => LibraryForm::forTrainer($trainerId)->count(),
            'assessments' => LibraryAssessment::forTrainer($trainerId)->count(),
            'videos' => LibraryVideo::forTrainer($trainerId)->count(),
            'documents' => LibraryDocument::forTrainer($trainerId)->count(),
            'habits' => LibraryHabit::forTrainer($trainerId)->count(),
            'meal_plans' => LibraryMealPlan::forTrainer($trainerId)->count(),
        ];

        $createdThisMonth = Program::forTrainer($trainerId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()
            + LibraryExercise::forTrainer($trainerId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()
            + LibraryForm::forTrainer($trainerId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()
            + LibraryAssessment::forTrainer($trainerId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()
            + LibraryVideo::forTrainer($trainerId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()
            + LibraryDocument::forTrainer($trainerId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()
            + LibraryHabit::forTrainer($trainerId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count()
            + LibraryMealPlan::forTrainer($trainerId)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $stats = [
            'createdThisMonth' => $createdThisMonth,
        ];

        $recentActivity = $this->buildRecentActivity($trainerId);

        return Inertia::render('Trainer/Library/Index', [
            'counts' => $counts,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Build recent activity list from latest items across all library tables.
     *
     * @return array<int, array{type: string, name: string, action: string, time: string, icon: string, color: string, bgColor: string}>
     */
    private function buildRecentActivity(int $trainerId): array
    {
        $entries = [];

        foreach (Program::forTrainer($trainerId)->orderBy('updated_at', 'desc')->limit(5)->get() as $p) {
            $entries[] = [
                'updated_at' => $p->updated_at,
                'type' => 'Program',
                'name' => $p->name,
                'action' => $p->updated_at->gt($p->created_at) ? 'updated' : 'created',
                'icon' => 'Dumbbell',
                'color' => 'text-blue-600',
                'bgColor' => 'bg-blue-100',
            ];
        }
        foreach (LibraryExercise::forTrainer($trainerId)->orderBy('updated_at', 'desc')->limit(5)->get() as $e) {
            $entries[] = [
                'updated_at' => $e->updated_at,
                'type' => 'Exercise',
                'name' => $e->name,
                'action' => $e->updated_at->gt($e->created_at) ? 'updated' : 'created',
                'icon' => 'Dumbbell',
                'color' => 'text-purple-600',
                'bgColor' => 'bg-purple-100',
            ];
        }
        foreach (LibraryForm::forTrainer($trainerId)->orderBy('updated_at', 'desc')->limit(5)->get() as $f) {
            $entries[] = [
                'updated_at' => $f->updated_at,
                'type' => 'Form',
                'name' => $f->name,
                'action' => $f->updated_at->gt($f->created_at) ? 'updated' : 'created',
                'icon' => 'FileInput',
                'color' => 'text-teal-600',
                'bgColor' => 'bg-teal-100',
            ];
        }
        foreach (LibraryAssessment::forTrainer($trainerId)->orderBy('updated_at', 'desc')->limit(5)->get() as $a) {
            $entries[] = [
                'updated_at' => $a->updated_at,
                'type' => 'Assessment',
                'name' => $a->name,
                'action' => $a->updated_at->gt($a->created_at) ? 'updated' : 'created',
                'icon' => 'ClipboardCheck',
                'color' => 'text-green-600',
                'bgColor' => 'bg-green-100',
            ];
        }
        foreach (LibraryVideo::forTrainer($trainerId)->orderBy('updated_at', 'desc')->limit(5)->get() as $v) {
            $entries[] = [
                'updated_at' => $v->updated_at,
                'type' => 'Video',
                'name' => $v->title,
                'action' => $v->updated_at->gt($v->created_at) ? 'updated' : 'uploaded',
                'icon' => 'Video',
                'color' => 'text-red-600',
                'bgColor' => 'bg-red-100',
            ];
        }
        foreach (LibraryDocument::forTrainer($trainerId)->orderBy('updated_at', 'desc')->limit(5)->get() as $d) {
            $entries[] = [
                'updated_at' => $d->updated_at,
                'type' => 'Document',
                'name' => $d->title,
                'action' => $d->updated_at->gt($d->created_at) ? 'updated' : 'uploaded',
                'icon' => 'File',
                'color' => 'text-orange-600',
                'bgColor' => 'bg-orange-100',
            ];
        }
        foreach (LibraryHabit::forTrainer($trainerId)->orderBy('updated_at', 'desc')->limit(5)->get() as $h) {
            $entries[] = [
                'updated_at' => $h->updated_at,
                'type' => 'Habit',
                'name' => $h->name,
                'action' => $h->updated_at->gt($h->created_at) ? 'updated' : 'created',
                'icon' => 'Heart',
                'color' => 'text-pink-600',
                'bgColor' => 'bg-pink-100',
            ];
        }
        foreach (LibraryMealPlan::forTrainer($trainerId)->orderBy('updated_at', 'desc')->limit(5)->get() as $m) {
            $entries[] = [
                'updated_at' => $m->updated_at,
                'type' => 'Meal Plan',
                'name' => $m->name,
                'action' => $m->updated_at->gt($m->created_at) ? 'updated' : 'created',
                'icon' => 'Utensils',
                'color' => 'text-yellow-600',
                'bgColor' => 'bg-yellow-100',
            ];
        }

        return collect($entries)
            ->sortByDesc('updated_at')
            ->take(8)
            ->map(function ($entry) {
                return [
                    'type' => $entry['type'],
                    'name' => $entry['name'],
                    'action' => $entry['action'],
                    'time' => Carbon::parse($entry['updated_at'])->diffForHumans(),
                    'icon' => $entry['icon'],
                    'color' => $entry['color'],
                    'bgColor' => $entry['bgColor'],
                ];
            })
            ->values()
            ->all();
    }
}
