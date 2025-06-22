<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\File;
use App\Models\Note;
use App\Models\Task;
use App\Models\Content;
use App\Models\Project;
use App\Models\Routine;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Count data
        $tasksCount = $user->tasks()->count();
        $routinesCount = $user->routines()->whereDate('start_time', now())->count();
        $notesCount = $user->notes()->count();
        $filesCount = $user->files()->count();

        // Recent data
        $recentTasks = $user->tasks()->latest()->take(5)->get();
        $todayRoutines = $user->routines()->whereDate('start_time', now())->take(5)->get();
        $recentNotes = $user->notes()->latest()->take(5)->get();
        $upcomingReminders = $user->reminders()
            ->where('date', '>=', now())
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        // Project list based on role
        if ($user->isCeo()) {
            $projects = Project::with('tasks')->get();
        } else {
            $projects = $user->projects()->with('tasks')->get();
        }

        $projectStatusCounts = $projects->groupBy('status')->map->count();

        // Chart TOPSIS
        $contents = Content::getPopularContent();
        $chartData = $contents->map(function ($content) {
            return [
                'name' => $content->title,
                'score' => $content->topsis_score,
            ];
        })->sortByDesc('score')->values();

        $selectedYear = request('year', now()->year); // default tahun sekarang

        $monthlyProjects = Project::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $projectMonthlyChart = collect(range(1, 12))->map(function ($month) use ($monthlyProjects) {
            return [
                'month' => Carbon::create()->month($month)->locale('id')->isoFormat('MMMM'),
                'total' => $monthlyProjects[$month] ?? 0,
            ];
        });


        return view('dashboard', compact(
            'tasksCount',
            'routinesCount',
            'notesCount',
            'filesCount',
            'recentTasks',
            'todayRoutines',
            'recentNotes',
            'upcomingReminders',
            'chartData',
            'projectStatusCounts',
            'projectMonthlyChart',
            'selectedYear'
        ));
    }
}
