@extends('layouts.app')
@section('title')
    Projects
@endsection
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded">
            <h2>Daftar konten</h2>
            @if (Auth::user() && Auth::user()->isMember() && !Auth::user()->isCeo())
                <a href="{{ route('projects.create') }}" class="btn btn-primary">Tambah konten</a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            @foreach ($projects as $project)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $project->name }}</h5>
                            <p class="card-text">{{ $project->description }}</p>
                            <p class="card-text">
                                <strong>Status:</strong>
                                {{ $project->status == 'pending' ? 'Pending' : ($project->status == 'on_going' ? 'In Progress' : 'Completed') }}<br>
                                <strong>Deadline:</strong>
                                @if ($project->end_date && $project->end_date->isFuture())
                                    {{ $project->end_date->diffForHumans() }}
                                @else
                                    <span class="text-danger">Deadline Passed</span>
                                @endif
                            </p>

                            {{-- Show view buttons to all users --}}
                            <a href="{{ route('projects.tasks.index', $project->id) }}" class="btn btn-primary"
                                title="View Tasks">
                                <i class="bi bi-list"></i>
                            </a>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary"
                                title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- Show edit and delete buttons only to members --}}
                            @if (Auth::user() && Auth::user()->isMember() && !Auth::user()->isCeo())
                                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Delete"
                                        onclick="return confirm('Are you sure you want to delete this daftar konten?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
