<form id="createProjectForm" action="{{ route('projects.store') }}" method="POST">
    @csrf
    <div class="modal-body">
        <div class="mb-3">
            <label for="create_name" class="form-label">Name</label>
            <input type="text" name="name" id="create_name" class="form-control" required>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="create_description" class="form-label">Description</label>
            <textarea name="description" id="create_description" class="form-control"></textarea>
            @error('description')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="create_start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="create_start_date" class="form-control"
                min="{{ date('Y-m-d') }}" required>
            @error('start_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="create_end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="create_end_date" class="form-control" min="{{ date('Y-m-d') }}">
            @error('end_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <input type="hidden" name="status" value="not_started" id="">
        {{-- <div class="mb-3">
            <label for="create_status" class="form-label">Status</label>
            <select name="status" id="create_status" class="form-select" required>
                <option value="not_started">Not Started</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
            @error('status')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div> --}}
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
