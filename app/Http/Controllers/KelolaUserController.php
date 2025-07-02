<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KelolaUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('KelolaUser.index', compact('users'));
    }

    public function create()
    {
        return view('KelolaUser.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'sometimes|in:member,admin,ceo' // Add validation for role
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'member', // Default to 'member' if not provided
            'phone_number' => $request->phone_number // Add if needed
        ]);

        return redirect()->route('KelolaUser.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('KelolaUser.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:member,admin,ceo'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role ?? $user->role; // Keep existing role if not provided

        if ($request->filled('phone_number')) {
            $user->phone_number = $request->phone_number;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('KelolaUser.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('KelolaUser.index')->with('success', 'User berhasil dihapus');
    }
}
