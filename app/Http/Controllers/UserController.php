<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    // Menampilkan form untuk mengedit profil pengguna yang sedang login
    public function edit()
    {
        $user = Auth::user(); // Mengambil data pengguna yang sedang login
        return view('auth.edit', compact('user'));
    }

    // Memperbarui profil pengguna yang sedang login
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|regex:/^8[1-9][0-9]{7,10}$/', // Validasi format 8xxxxxxxxxx (tanpa 0)
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        // Tambahkan 0 di depan sebelum simpan ke database
        $user->phone_number = '0' . $validated['phone_number'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('dashboard')->with('success', 'Profil berhasil diperbarui');
    }
}
