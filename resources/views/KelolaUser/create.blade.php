@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah User</h2>
    <form action="{{ route('KelolaUser.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('KelolaUser.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
