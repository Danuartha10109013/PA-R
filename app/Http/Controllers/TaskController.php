<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            // If user is CEO, allow access to all tasks
            if (Auth::user()->isCeo()) {
                return $next($request);
            }

            $project = $request->route('project');
            if ($project && !($project->user_id === Auth::id() || $project->users->contains(Auth::id()))) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index(Project $project)
    {
        $tasks = $project->tasks()->get()->groupBy('status');
        $users = $project->users()->get();
        return view('tasks.index', compact('project', 'tasks', 'users'));
    }

    public function store(Request $request, Project $project)
    {
        if (!Auth::user()->isMember()) {
            abort(403, 'Only members can create tasks.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|string'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id(); // Set the user_id to the authenticated user

        $project->tasks()->create($data);

        return redirect()->route('projects.tasks.index', $project)->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:perencanaan,pembuatan,pengeditan,peninjauan,publikasi',
        ]);

        $task->update($request->all());

        return redirect()->route('projects.tasks.index', $task->project_id)->with('success', 'Task updated successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $task->status = $request->input('status');
        $task->save();

        return response()->json(['message' => 'Task status updated successfully.']);
    }
}
