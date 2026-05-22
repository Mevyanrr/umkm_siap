<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        return view('umkm.profile_umkm', ['user' => Auth::user()]);
    }

    public function update(Request $request)
{
    // Ambil data user yang sedang login langsung melalui Model User
    /** @var \App\Models\User $user */
    $user = \App\Models\User::find(Auth::id());

    if (!$user) {
        return redirect()->back()->with('error', 'User tidak ditemukan.');
    }

    $request->validate([
        'name'     => 'required|string|max:255',
        'phone'    => 'nullable|string|max:20',
        'provinsi' => 'nullable|string|max:100',
        'email'    => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:6|confirmed',
    ], [
        'name.required'     => 'Nama wajib diisi.',
        'email.unique'      => 'Email sudah digunakan.',
        'password.min'      => 'Password minimal 6 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ]);

    $user->name     = $request->name;
    $user->email    = $request->email;
    $user->phone    = $request->phone;
    $user->provinsi = $request->provinsi;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save(); // <--- DIJAMIN AMAN DAN LEGAL

    return redirect()->route('umkm.profile')->with('success', 'Profile berhasil diperbarui!');
}
}
