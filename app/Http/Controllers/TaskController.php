<?php
namespace App\Http\Controllers;

use App\Jobs\SendTaskReminder;
use App\Mail\TaskNewMail;
use App\Mail\TaskScheduledMail;
use App\Models\NotificationM;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
            'description' => 'required|string',
            'due_date' => 'required|date',
            'status' => 'required|string'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id(); // Set the user_id to the authenticated user
        $user = Auth::user();
        $isCEO = $user->isCeo();

        
        $task = $project->tasks()->create($data);

        $notif = new NotificationM();
        $notif->title = 'Task Baru telah ditambahkan '. $request->title;
        $notif->content = 'Task telah ditambahkan oleh ' . Auth::user()->name . 'dengan deadline pada ' . $request->due_date;
        $notif->status_ceo = 0;
        $notif->status_marketing = 0;
        $notif->user_id = Auth::user()->id;
        $notif->projects_id = $project->id;
        $notif->tasks_id = $task->id;
        $notif->save();

        SendTaskReminder::dispatch($task)->delay(
            \Carbon\Carbon::parse($task->due_date)->subDay()
        );


        // dd($task);
        // Kirim email langsung ke user
        Mail::to($user->email)->send(new TaskNewMail($task, $user, $isCEO)); //ygini

        // // Kirim email terjadwal ke user

        // Mail::to($user->email)
        //     ->later(now()->addMinutes(1), new ScheduledMail($task, $user, $isCEO));
        //kalo tar mau nyobain demo, jeda waktu 1 menit nyalain 2 baris atas
        // Kirim email satu hari sebelum deadline nah kalo gini tar si email yang remindernya kekirim  1 hari sebelum end date
        if ($task->due_date) {
            $scheduledDate = Carbon::parse($task->due_date)->subDay();

            Mail::to($user->email)
                ->later($scheduledDate, new TaskScheduledMail($task, $user, $isCEO));
        }
        
        // // Kirim email ke semua CEO
        $ceos = User::where('role', 'ceo')->get();
        foreach ($ceos as $ceoUser) {
            $user = User::find($ceoUser->id);
            Mail::to($user->email)->send(new TaskNewMail($task, $user, true));
            Mail::to($user->email)
                ->later($scheduledDate, new TaskScheduledMail($task, $user, $isCEO));
        }
        // dd($ceos);
        // SendTaskReminder::dispatch($task)->delay(now()->addMinutes(1));
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
            'description' => 'required|string',
            'due_date' => 'required|date',
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
