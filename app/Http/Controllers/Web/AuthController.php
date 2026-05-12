<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Handle register UMKM & Buyer dari form Blade
    public function register(Request $request)
    {
        $role = $request->input('role');

        if ($role === 'umkm') {
            $request->validate([
                'nama_usaha' => 'required|string|max:255',
                'email'      => 'required|email|unique:users,email',
                'password'   => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ], [
                'nama_usaha.required' => 'Nama usaha wajib diisi.',
                'email.unique'        => 'Email sudah terdaftar.',
                'password.regex'      => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            ]);

            $user = User::create([
                'name'     => $request->nama_usaha,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'umkm',
            ]);

            Auth::login($user);
            return redirect()->route('dashboard.umkm');

        } elseif ($role === 'buyer') {
            $request->validate([
                'nama_pengusaha' => 'required|string|max:255',
                'email'          => 'required|email|unique:users,email',
                'password'       => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ], [
                'nama_pengusaha.required' => 'Nama pengusaha wajib diisi.',
                'email.unique'            => 'Email sudah terdaftar.',
                'password.regex'          => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            ]);

            $user = User::create([
                'name'     => $request->nama_pengusaha,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'buyer',
            ]);

            Auth::login($user);
            return redirect()->route('dashboard.buyer');

        } else {
            return redirect()->back()->withErrors(['role' => 'Role tidak valid.']);
        }
    }
}
