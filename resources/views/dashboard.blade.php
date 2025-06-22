@extends('layouts.app')
@section('title')
    Dashboard
@endsection

@section('content')
    <div class="container">
        {{-- ==== STATUS PROYEK ==== --}}
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-3">Status Proyek</h4>
            </div>

            {{-- Pending --}}
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-bg-warning shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title">Pending</h6>
                        <h2 class="fw-bold">{{ $projectStatusCounts['pending'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            {{-- On Going --}}
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-bg-info shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title">On Going</h6>
                        <h2 class="fw-bold">{{ $projectStatusCounts['on_going'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            {{-- Unfinished --}}
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-bg-danger shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title">Unfinished</h6>
                        <h2 class="fw-bold">{{ $projectStatusCounts['unfinished'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            {{-- Finished --}}
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card text-bg-success shadow-sm h-100">
                    <div class="card-body text-center">
                        <h6 class="card-title">Finished</h6>
                        <h2 class="fw-bold">{{ $projectStatusCounts['finished'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->user()->isCeo())
            <form action="{{ route('send.project.reminder') }}" method="GET"
                onsubmit="return confirm('Yakin kirim reminder sekarang?')">
                <button type="submit" class="btn btn-primary mb-3">
                    <i class="bi bi-envelope"></i> Kirim Reminder Proyek (Manual)
                </button>
            </form>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        {{-- ==== CHART PROYEK PER BULAN ==== --}}
        <form method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label for="year" class="form-label">Pilih Tahun</label>
                    <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                        @for ($i = now()->year; $i >= now()->year - 4; $i--)
                            <option value="{{ $i }}" {{ request('year', now()->year) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title m-0">Jumlah Proyek per Bulan ({{ $selectedYear }})</h5>
                    </div>
                    <div class="card-body" style="height: 400px;">
                        <canvas id="projectMonthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        {{-- ==== CHART RANKING ==== --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title m-0">Ranking Konten</h5>
                    </div>
                    <div class="card-body" style="height: 400px;">
                        <canvas id="topsisChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

        // Function chart per bulan
        const monthlyCtx = document.getElementById('projectMonthlyChart').getContext('2d');
        const monthlyData = @json($projectMonthlyChart);

        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Jumlah Proyek',
                    data: monthlyData.map(item => item.total),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Proyek'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Bulan'
                        }
                    }
                }
            }
        });
    </script>
@endpush
