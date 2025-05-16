<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->isCeo()) {
                // Allow only index and show methods for CEO
                if (!in_array($request->route()->getActionMethod(), ['index', 'show'])) {
                    abort(403, 'CEO role can only view projects.');
                }
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    public function index()
    {
        if (Auth::user()->isCeo()) {
            // CEO can see all projects
            $projects = Project::withCount(['tasks as perencanaan_tasks' => function ($query) {
                $query->where('status', 'perencanaan');
            }, 'tasks as pembuatan_tasks' => function ($query) {
                $query->where('status', 'pembuatan');
            }, 'tasks as pengeditan_tasks' => function ($query) {
                $query->where('status', 'pengeditan');
            }, 'tasks as peninjauan_tasks' => function ($query) {
                $query->where('status', 'peninjauan');
            }, 'tasks as publikasi_tasks' => function ($query) {
                $query->where('status', 'publikasi');
            }])->get();
        } else {
            // Other users see only their projects
            $projects = Project::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereHas('users', function($q) {
                          $q->where('users.id', Auth::id());
                      });
            })->withCount(['tasks as perencanaan_tasks' => function ($query) {
                $query->where('status', 'perencanaan');
            }, 'tasks as pembuatan_tasks' => function ($query) {
                $query->where('status', 'pembuatan');
            }, 'tasks as pengeditan_tasks' => function ($query) {
                $query->where('status', 'pengeditan');
            }, 'tasks as peninjauan_tasks' => function ($query) {
                $query->where('status', 'peninjauan');
            }, 'tasks as publikasi_tasks' => function ($query) {
                $query->where('status', 'publikasi');
            }])->get();
        }

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->isCeo()) {
            abort(403, 'CEO role cannot create projects.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:not_started,in_progress,completed',
            'budget' => 'nullable|numeric',
        ]);

        Auth::user()->projects()->create($request->all());

        return redirect()->route('projects.index')->with('success', 'Daftar konten created successfully.');
    }

    public function show(Project $project)
    {
        $teamMembers = $project->users()->get();
        $users = User::all();
        return view('projects.show', compact('project', 'teamMembers', 'users'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        if (Auth::user()->isCeo()) {
            abort(403, 'CEO role cannot update projects.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:not_started,in_progress,completed',
            'budget' => 'nullable|numeric',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')->with('success', 'Daftar konten updated successfully.');
    }

    public function destroy(Project $project)
    {
        if (Auth::user()->isCeo()) {
            abort(403, 'CEO role cannot delete projects.');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Daftar konten deleted successfully.');
    }

    public function addMember(Request $request)
    {
        if (!Auth::user()->isMember()) {
            abort(403, 'Only members can add team members to projects.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $project = Project::findOrFail($request->project_id);
        $project->users()->attach($request->user_id);

        return redirect()->back()->with('success', 'Team member added successfully.');
    }
}
