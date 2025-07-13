<?php

namespace App\Http\Controllers;

use App\Mail\ImmediateMail;
use App\Mail\ScheduledMail;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use App\Models\ReminderProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator; // Import Validator for manual validation

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->isCeo()) {
                // Allow only index and show methods for CEO
                if (!in_array($request->route()->getActionMethod(), ['index', 'show', 'getProjectData'])) { // Allow getProjectData for CEO to view details
                    abort(403, 'CEO role can only view projects.');
                }
            }
            return $next($request);
        })->except(['index', 'show']); // Keep standard for other roles, getProjectData is explicitly allowed for CEO above
    }

    public function index()
    {
        // dd(request('status'));
        if (Auth::user()->isCeo()) {
            $projects = Project::with('tasks')
                ->withCount([
                    'tasks as perencanaan_tasks' => fn($q) => $q->where('status', 'perencanaan'),
                    'tasks as pembuatan_tasks' => fn($q) => $q->where('status', 'pembuatan'),
                    'tasks as pengeditan_tasks' => fn($q) => $q->where('status', 'pengeditan'),
                    'tasks as peninjauan_tasks' => fn($q) => $q->where('status', 'peninjauan'),
                    'tasks as publikasi_tasks' => fn($q) => $q->where('status', 'publikasi'),
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        else {
            $projects = Project::where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('users', fn($q) => $q->where('users.id', Auth::id()));
            })
                ->with('tasks')
                ->withCount([
                    'tasks as perencanaan_tasks' => fn($q) => $q->where('status', 'perencanaan'),
                    'tasks as pembuatan_tasks' => fn($q) => $q->where('status', 'pembuatan'),
                    'tasks as pengeditan_tasks' => fn($q) => $q->where('status', 'pengeditan'),
                    'tasks as peninjauan_tasks' => fn($q) => $q->where('status', 'peninjauan'),
                    'tasks as publikasi_tasks' => fn($q) => $q->where('status', 'publikasi'),
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // ✅ Hitung jumlah berdasarkan accessor status (dinamis)
        $statusCounts = $projects->groupBy('status')->map->count();

        // ✅ Filter jika ada parameter ?status=
        if (request()->has('status') && request('status') !== 'all') {
            $projects = $projects->filter(fn($project) => $project->status === request('status'))->values();
        }

        return view('projects.index', compact('projects', 'statusCounts'));
    }


    // New method to fetch project data for the modal
    public function getProjectData(Project $project)
    {
        // Add authorization check if needed, e.g., only project owner/member can get data
        if (Auth::user()->isMember() && $project->user_id !== Auth::id() && !$project->users->contains(Auth::id())) {
            if (!Auth::user()->isCeo()) { // CEOs can see all, so no 403 for them here.
                abort(403, 'Unauthorized to view this project data.');
            }
        }

        return response()->json($project);
    }

    public function create()
    {
        return view('projects.create');
    }

    // public function store(Request $request)
    // {
    //     if (Auth::user()->isCeo()) {
    //         abort(403, 'CEO role cannot create projects.');
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255|unique:projects',
    //         'description' => 'nullable|string',
    //         'start_date' => 'required|date|after_or_equal:today',
    //         'end_date' => 'nullable|date|after_or_equal:start_date',
    //         'status' => 'required|in:not_started,in_progress,completed',
    //         'budget' => 'nullable|numeric',
    //     ]);

    //     if ($validator->fails()) {
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validation failed',
    //                 'errors' => $validator->errors()
    //             ], 422);
    //         }
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     try {
    //         $project = Auth::user()->projects()->create($validator->validated());

    //         $startDate = Carbon::parse($project->start_date);
    //         $reminderDate = $startDate->subDay();

    //         ReminderProject::create([
    //             'project_id' => $project->id,
    //             'user_id' => $project->user_id,
    //             'reminder_date' => $reminderDate->toDateString(),
    //         ]);

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'redirect' => route('projects.index')
    //             ]);
    //         }

    //         $user = Auth::user();
    //         $isCEO = $user->isCeo();

    //         // Kirim email langsung ke user
    //         try {
    //             Mail::to($user->email)->send(new ImmediateMail($project, $user, $isCEO));
    //             \Log::info("Email immediate berhasil dikirim ke: " . $user->email);
    //         } catch (\Exception $e) {
    //             \Log::error("Gagal kirim email immediate ke {$user->email}: " . $e->getMessage());
    //         }

    //         // Kirim email terjadwal ke user
    //         try {
    //             Mail::to($user->email)
    //                 ->later(now()->addMinutes(5), new ScheduledMail($project, $user, $isCEO));
    //             \Log::info("Email scheduled berhasil dijadwalkan untuk: " . $user->email);
    //         } catch (\Exception $e) {
    //             \Log::error("Gagal menjadwalkan email ke {$user->email}: " . $e->getMessage());
    //         }

    //         // Kirim email ke CEO
    //         $ceos = User::where('role', 'ceo')->get();
    //         foreach ($ceos as $c) {
    //             $user = User::find($c->id);

    //             try {
    //                 Mail::to($user->email)->send(new ImmediateMail($project, $user, true));
    //                 \Log::info("Email immediate berhasil dikirim ke CEO: " . $user->email);
    //             } catch (\Exception $e) {
    //                 \Log::error("Gagal kirim email immediate ke CEO {$user->email}: " . $e->getMessage());
    //             }

    //             try {
    //                 Mail::to($user->email)
    //                     ->later(now()->addMinutes(5), new ScheduledMail($project, $user, true));
    //                 \Log::info("Email scheduled berhasil dijadwalkan untuk CEO: " . $user->email);
    //             } catch (\Exception $e) {
    //                 \Log::error("Gagal menjadwalkan email ke CEO {$user->email}: " . $e->getMessage());
    //             }
    //         }

    //         return redirect()->route('projects.index')->with('success', 'Project created successfully and reminder set.');
    //     } catch (\Exception $e) {
    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Terjadi kesalahan saat membuat project: ' . $e->getMessage()
    //             ], 500);
    //         }
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat project: ' . $e->getMessage());
    //     }
    // }

    public function store(Request $request)
{
    //ini function store sama kirim email nya
    if (Auth::user()->isCeo()) {
        abort(403, 'CEO role cannot create projects.');
    }

    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255|unique:projects',
        'description' => 'nullable|string',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'status' => 'required|in:not_started,in_progress,completed',
        'budget' => 'nullable|numeric',
    ]);

    if ($validator->fails()) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $project = Auth::user()->projects()->create($validator->validated());

    $startDate = Carbon::parse($project->start_date);
    $reminderDate = $startDate->subDay();

    ReminderProject::create([
        'project_id' => $project->id,
        'user_id' => $project->user_id,
        'reminder_date' => $reminderDate->toDateString(),
    ]);

    $user = Auth::user();
    $isCEO = $user->isCeo();

    // Kirim email langsung ke user
    Mail::to($user->email)->send(new ImmediateMail($project, $user, $isCEO));

    // Kirim email terjadwal ke user
    $scheduledDate = Carbon::parse($project->end_date)->subDay(); 

    Mail::to($user->email)
        ->later(now()->addMinutes(1), new ScheduledMail($project, $user, $isCEO));
    //kalo tar mau nyobain demo, jeda waktu 1 menit nyalain 2 baris atas
    
    // Kirim email satu hari sebelum deadline nah kalo gini tar si email yang remindernya kekirim  1 hari sebelum end date
    Mail::to($user->email)
        ->later($scheduledDate, new ScheduledMail($project, $user, $isCEO)); //yang ini matiin

    // Kirim email ke semua CEO
    $ceos = User::where('role', 'ceo')->get();
    foreach ($ceos as $ceoUser) {
        $user = User::find($ceoUser->id);
        Mail::to($user->email)->send(new ImmediateMail($project, $user, true));
        Mail::to($user->email)
            ->later($scheduledDate, new ScheduledMail($project, $user, $isCEO));
    }

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'redirect' => route('projects.index')
        ]);
    }

    return redirect()->route('projects.index')->with('success', 'Project created and emails sent.');
}

    public function show(Project $project)
    {
        $teamMembers = $project->users()->get();
        $users = User::all();
        return view('projects.show', compact('project', 'teamMembers', 'users'));
    }

    // This 'edit' method is now essentially unused if you're using the modal,
    // but you can leave it for now or remove the route to it.
    public function edit(Project $project)
    {
        // This view is no longer directly used if editing via modal.
        // It might be useful if you still want a dedicated edit page.
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        if (Auth::user()->isCeo()) {
            abort(403, 'CEO role cannot update projects.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:not_started,in_progress,completed',
            'budget' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $project->update($validator->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('projects.index')
                ]);
            }
            return redirect()->route('projects.index')->with('success', 'Project content updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memperbarui konten: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui konten: ' . $e->getMessage());
        }
    }

    public function destroy(Project $project)
    {
        if (Auth::user()->isCeo()) {
            abort(403, 'CEO role cannot delete projects.');
        }

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project content deleted successfully.');
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
