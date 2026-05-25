<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BuyerProfileController extends Controller
{
    public function index()
    {
        return view('buyer.profile_buyer', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = \App\Models\User::find(Auth::id());

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.unique'       => 'Email sudah digunakan.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('buyer.profile')->with('success', 'Profil berhasil diperbarui!');
    }
}