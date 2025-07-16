@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Kelola User</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCreateUser">+ Add User</button>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    {{-- <th>No</th> --}}
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        {{-- <td>{{ $loop->iteration }}</td> --}}
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if ($user->role === 'ceo')
                                {{ strtoupper($user->role) }} <!-- Hasil: CEO -->
                            @else
                                {{ ucfirst($user->role) }}
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning btn-edit" data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                data-role="{{ $user->role }}" data-bs-toggle="modal" data-bs-target="#modalEditUser">
                                Edit
                            </button>
                            <!-- Tombol untuk membuka modal -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $user->id }}">
                                Delete
                            </button>

                            <!-- Modal per user -->
                            <div class="modal fade" id="modalDelete{{ $user->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <form action="{{ route('KelolaUser.destroy', $user->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel{{ $user->id }}">Confirmation Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                    </div>
                                    <div class="modal-body">
                                    Are you sure you want to delete the user <strong>{{ $user->name }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Yes</button>
                                    </div>
                                </form>
                                </div>
                            </div>
                            </div>


                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal Create --}}
    <div class="modal fade" id="modalCreateUser" data-bs-backdrop="static" tabindex="-1" aria-labelledby="createUserLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('KelolaUser.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserLabel">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="password_create" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword_create">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                                <option value="">--Pilih Role--</option>
                                <option value="member">Member</option>
                                <option value="ceo">CEO</option>
                            </select>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="modalEditUser" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editUserLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditUser" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>New Password (leave blank if you don't want to replace)</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="password_edit">
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword_edit">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        {{-- <div class="mb-3">
                            <label>Role</label>
                            <select name="role" id="edit_role" class="form-control" required>
                                <option value="">--Pilih Role--</option>
                                <option value="member">Member</option>
                                <option value="ceo">CEO</option>
                            </select>
                        </div>
                    </div> --}}
                    <div class="modal-footer">
                        <button class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle password Edit
document.getElementById('togglePassword_edit').addEventListener('click', function() {
    const pass = document.getElementById('password_edit');
    const type = pass.getAttribute('type') === 'password' ? 'text' : 'password';
    pass.setAttribute('type', type);
    this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
});

// Fill modal Edit
document.querySelectorAll('.btn-edit').forEach(button => {
    button.addEventListener('click', function () {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const email = this.dataset.email;
        const role = this.dataset.role;

        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('password_edit').value = ''; // Kosongkan input password saat edit

        // Set nilai role jika select tersedia
        const roleSelect = document.getElementById('edit_role');
        if (roleSelect) {
            roleSelect.value = role;
        }

        const form = document.getElementById('formEditUser');
        form.action = `/KelolaUser/${id}`;
    });
});

    </script>
@endpush
