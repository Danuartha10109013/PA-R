@extends('layouts.app')
@section('title')
    Project
@endsection
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
            timer: 3000,
            timerProgressBar: true
        });
    @elseif (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
            timer: 4000,
            timerProgressBar: true
        });
    @endif
</script>

    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const modal = new bootstrap.Modal(document.getElementById('createProjectModal'));
                modal.show();
            });
        </script>
    @endif

    <div class="container">
        <div class="d-flex justify-content-between align-items-center bg-white mb-4 shadow-sm p-3 rounded flex-wrap gap-2">
            <h2 class="mb-0">Project Content</h2>

            <div class="d-flex align-items-center gap-2">
                {{-- Form Filter --}}
                <form method="GET" action="{{ route('projects.index') }}">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="all">Semua Status</option>
                        <option value="not_started" {{ request('status') == 'not_started' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                        </option>
                        
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </form>

                {{-- Tombol Tambah Project --}}
                @if (Auth::user() && Auth::user()->isMember() && !Auth::user()->isCeo())
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#createProjectModal">
                        Add project content
                    </button>
                @endif
            </div>
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
                                {{-- {{ $project->status == 'not_started' ? 'Pending' : ($project->status == 'in_progres' ? 'In Progress' : ($project->status == 'unfinished' ? 'Unfinished' : ($project->status == 'completed' ? 'Completed' : 'Unknown'))) }} <br> --}}
                                @php
                                    // $sts = \App\Models\Project::find($project->id)->value('status');
                                    // dd($sts);
                                    $statusMap = [
                                        'not_started' => ['label' => 'Pending', 'class' => 'secondary', 'icon' => 'bi-clock'],
                                        'in_progress' => ['label' => 'On Going', 'class' => 'warning text-dark', 'icon' => 'bi-arrow-repeat'],
                                        'completed' => ['label' => 'Completed', 'class' => 'success', 'icon' => 'bi-check-circle'],
                                    ];

                                    $status = $statusMap[$project->status] ?? ['label' => 'Unknown', 'class' => 'dark', 'icon' => 'bi-question-circle'];
                                @endphp

                                <span class="badge bg-{{ $status['class'] }}">
                                    <i class="bi {{ $status['icon'] }}"></i> {{ $status['label'] }}
                                </span> <br>
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
                            {{-- <a href="{{ route('projects.show', $project->id) }}" class="btn btn-primary"
                                title="View Details">
                                <i class="bi bi-eye"></i>
                            </a> --}}

                            {{-- Show edit and delete buttons only to members --}}
                            @if (Auth::user() && Auth::user()->isMember() && !Auth::user()->isCeo())
                                <button type="button" class="btn btn-warning" title="Edit" data-bs-toggle="modal"
                                    data-bs-target="#editProjectModal" data-id="{{ $project->id }}"
                                    data-name="{{ $project->name }}" data-description="{{ $project->description }}"
                                    data-start-date="{{ \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') }}"
                                    data-end-date="{{ \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') }}"
                                    data-status="{{ $project->status }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                                    class="d-inline" id="deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger delete-btn" title="Delete"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
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

    <!-- Create Project Modal -->
    <div class="modal fade" id="createProjectModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="createProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProjectModalLabel">Add project content</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                @include('modal.add-project')
            </div>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div class="modal fade" id="editProjectModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="editProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                @include('modal.edit-project')
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this project?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set up date validation for create form
            const createStartDate = document.getElementById('create_start_date');
            const createEndDate = document.getElementById('create_end_date');

            createStartDate.addEventListener('change', function() {
                createEndDate.min = this.value;
            });

            // Set up date validation for edit form
            const editStartDate = document.getElementById('edit_start_date');
            const editEndDate = document.getElementById('edit_end_date');

            editStartDate.addEventListener('change', function() {
                editEndDate.min = this.value;
            });

            // Edit modal setup
            var editProjectModal = document.getElementById('editProjectModal');
            editProjectModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var projectId = button.getAttribute('data-id');
                var projectName = button.getAttribute('data-name');
                var projectDescription = button.getAttribute('data-description');
                var projectStartDate = button.getAttribute('data-start-date');
                var projectEndDate = button.getAttribute('data-end-date');
                var projectStatus = button.getAttribute('data-status');

                var modalTitle = editProjectModal.querySelector('.modal-title');
                var nameInput = editProjectModal.querySelector('#edit_name');
                var descriptionInput = editProjectModal.querySelector('#edit_description');
                var startDateInput = editProjectModal.querySelector('#edit_start_date');
                var endDateInput = editProjectModal.querySelector('#edit_end_date');
                var statusInput = editProjectModal.querySelector('#edit_status');
                var form = editProjectModal.querySelector('#editProjectForm');

                modalTitle.textContent = 'Edit Project: ' + projectName;
                nameInput.value = projectName;
                descriptionInput.value = projectDescription;
                startDateInput.value = projectStartDate;
                endDateInput.value = projectEndDate;
                statusInput.value = projectStatus;

                form.action = '/projects/' + projectId;
            });

            // Handle form submission with AJAX
            $('#editProjectForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + 'edit_' + key).addClass('is-invalid');
                                $('#' + 'edit_' + key).after(
                                    '<div class="invalid-feedback">' + value[0] +
                                    '</div>');
                            });
                        } else {
                            alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                        }
                    }
                });
            });

            // Handle create form submission with AJAX
            $('#createProjectForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');

                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + 'create_' + key).addClass('is-invalid');
                                $('#' + 'create_' + key).after(
                                    '<div class="invalid-feedback">' + value[0] +
                                    '</div>');
                            });
                        } else {
                            alert('Terjadi kesalahan: ' + xhr.responseJSON.message);
                        }
                    }
                });
            });

            // Remove validation errors when modals are hidden
            $('#editProjectModal, #createProjectModal').on('hidden.bs.modal', function() {
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').remove();
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Tangani klik tombol delete
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Set form yang akan di-submit ketika konfirmasi
                    const form = this.closest('form');
                    document.getElementById('confirmDelete').onclick = function() {
                        form.submit();
                    };
                });
            });
        });
    </script>
@endpush
