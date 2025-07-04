@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Add User</h2>
        <form action="{{ route('KelolaUser.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
                <div class="mb-3">
                    <label>Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" id="password_create" required>
                        <button type="button" class="btn btn-outline-secondary" id="togglePassword_create">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button class="btn btn-success">Done</button>
                <a href="{{ route('KelolaUser.index') }}" class="btn btn-secondary">Close</a>
        </form>
    </div>

    @push('scripts')
        <script>
            document.getElementById('togglePassword_create').addEventListener('click', function() {
                const passwordInput = document.getElementById('password_create');
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ?
                    '<i class="bi bi-eye"></i>' :
                    '<i class="bi bi-eye-slash"></i>';

            });
        </script>
    @endpush
@endsection
