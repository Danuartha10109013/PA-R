@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit User</h2>
    <form action="{{ route('KelolaUser.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
            <label>Password Baru (kosongkan jika tidak ingin mengganti)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('KelolaUser.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
