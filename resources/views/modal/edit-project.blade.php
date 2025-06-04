<form id="editProjectForm" method="POST">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="mb-3">
            <label for="edit_name" class="form-label">Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edit_description" class="form-label">Description</label>
            <textarea name="description" id="edit_description" class="form-control"></textarea>
            @error('description')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edit_start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="edit_start_date" class="form-control" min="{{ date('Y-m-d') }}">
            @error('start_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edit_end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="edit_end_date" class="form-control" min="{{ date('Y-m-d') }}">
            @error('end_date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edit_status" class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-select" required>
                <option value="not_started">Not Started</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
            @error('status')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Selesai</button>
    </div>
</form>
