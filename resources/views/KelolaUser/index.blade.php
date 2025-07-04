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
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
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
                            <form action="{{ route('KelolaUser.destroy', $user->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Hapus user ini?')">Delete</button>
                            </form>
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
                        <button class="btn btn-success">Done</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                            <label>New Password (kosongkan jika tidak ingin mengganti)</label>
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
                        <button class="btn btn-primary">Done</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle password Create
        document.getElementById('togglePassword_create').addEventListener('click', function() {
            const pass = document.getElementById('password_create');
            const type = pass.getAttribute('type') === 'password' ? 'text' : 'password';
            pass.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });

        // Toggle password Edit
        document.getElementById('togglePassword_edit').addEventListener('click', function() {
            const pass = document.getElementById('password_edit');
            const type = pass.getAttribute('type') === 'password' ? 'text' : 'password';
            pass.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
        });

        // Fill modal Edit
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const email = this.dataset.email;
                const role = this.dataset.role;

                document.getElementById('edit_name').value = name;
                document.getElementById('edit_email').value = email;

                // Set nilai role
                const roleSelect = document.getElementById('edit_role');
                roleSelect.value = role;

                const form = document.getElementById('formEditUser');
                form.action = `/KelolaUser/${id}`;
            });
        });
    </script>
@endpush
