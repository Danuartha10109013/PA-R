@extends('layouts.app')
@section('title')
    Dashboard
@endsection
@section('content')
    <div class="container">
        <h2></h2>
        <!-- <p>This is your dashboard where you can manage your tasks, routines, notes, and files.</p> -->

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title m-0">Ranking konten</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topsisChart" style="height: 400px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="row mb-4">
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Tasks</h5>
                        <p class="card-text flex-grow-1">You have <strong>{{ $tasksCount }}</strong> tasks pending.</p>
                        <a href="{{ route('projects.index') }}" class="btn btn-primary mt-auto">View Tasks</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Routines</h5>
                        <p class="card-text flex-grow-1">You have <strong>{{ $routinesCount }}</strong> routines scheduled
                            today.</p>
                        <a href="{{ route('routines.index') }}" class="btn btn-primary mt-auto">View Routines</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Notes</h5>
                        <p class="card-text flex-grow-1">You have <strong>{{ $notesCount }}</strong> notes saved.</p>
                        <a href="{{ route('notes.index') }}" class="btn btn-primary mt-auto">View Notes</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Files</h5>
                        <p class="card-text flex-grow-1">You have <strong>{{ $filesCount }}</strong> files.</p>
                        <a href="{{ route('files.index') }}" class="btn btn-primary mt-auto">View Files</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Recent Tasks</h5>
                        <ul class="list-group flex-grow-1">
                            @foreach ($recentTasks as $task)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $task->title }}
                                    <span
                                        class="badge bg-primary rounded-pill">{{ $task->status == 'perencanaan' ? 'Perencanaan' : 'Pembuatan' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Today's Routines</h5>
                        <ul class="list-group flex-grow-1">
                            @foreach ($todayRoutines as $routine)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $routine->title }}
                                    <span class="badge bg-primary rounded-pill">{{ $routine->frequency }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Recent Notes</h5>
                        <ul class="list-group flex-grow-1">
                            @foreach ($recentNotes as $note)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $note->title }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Upcoming Reminders</h5>
                        <ul class="list-group flex-grow-1">
                            @foreach ($upcomingReminders as $reminder)
                                @php
                                    $reminderDate = \Carbon\Carbon::parse($reminder->date);
                                @endphp
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center {{ $reminderDate->isToday() ? 'bg-warning' : ($reminderDate->isPast() ? 'bg-danger' : 'bg-success') }}">
                                    {{ $reminder->title }}
                                    <span class="badge bg-primary rounded-pill">{{ $reminderDate->format('M d') }}
                                        {{ $reminder->time ? \Carbon\Carbon::parse($reminder->time)->format('H:i') : '' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('topsisChart').getContext('2d');
    const chartData = @json($chartData);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(item => item.name),
            datasets: [{
                label: 'TOPSIS Score',
                data: chartData.map(item => item.score),
                backgroundColor: chartData.map((_, index) => {
                    const value = index / chartData.length;
                    return `rgba(54, 162, 235, ${1 - value * 0.6})`; // Gradient blue
                }),
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Rankings Content'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'TOPSIS Score'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Content'
                    }
                }
            }
        }
    });
});
</script>
@endpush
