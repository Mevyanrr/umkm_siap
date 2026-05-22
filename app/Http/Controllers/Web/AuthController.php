<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $role = $request->input('role');

        // =========================
        // REGISTER UMKM
        // =========================
        if ($role === 'umkm') {

            $request->validate([
                'nama_usaha' => 'required|string|max:255',
                'email'      => 'required|email|unique:users,email',
                'password'   => 'required|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ], [
                'nama_usaha.required' => 'Nama usaha wajib diisi.',
                'email.unique'        => 'Email harus menggunakan @gamil.com',
                'password.regex'      => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            ]);

            $user = User::create([
                'name'     => $request->nama_usaha,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'umkm',
            ]);

            Auth::login($user, false);
            $request->session()->regenerate();

            $token = JWTAuth::fromUser($user);
            session(['jwt_token' => $token]);

            return redirect()->route('dashboard.umkm');
        }

        // =========================
        // REGISTER BUYER
        // =========================
        elseif ($role === 'buyer') {

            $request->validate([
                'nama_pengusaha' => 'required|string|max:255',
                'email'          => 'required|email|unique:users,email',
                'password'       => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ], [
                'nama_pengusaha.required' => 'Nama pengusaha wajib diisi.',
                'email.unique'            => 'Email harus menggunakan @gamil.com',
                'password.regex'          => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
            ]);

            $user = User::create([
                'name'     => $request->nama_pengusaha,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'buyer',
            ]);

            Auth::login($user, false);
            $request->session()->regenerate();

            $token = JWTAuth::fromUser($user);
            session(['jwt_token' => $token]);

            return redirect()->route('dashboard.buyer');
        }

        // =========================
        // ROLE TIDAK VALID
        // =========================
        else {
            return redirect()->back()->withErrors([
                'role' => 'Role tidak valid.'
            ]);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        Auth::login($user, false);
        $request->session()->regenerate();

        $token = JWTAuth::fromUser($user);
        session(['jwt_token' => $token]);

        if ($user->role === 'umkm') {
            return redirect()->route('dashboard.umkm');
        } elseif ($user->role === 'buyer') {
            return redirect()->route('dashboard.buyer');
        }

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (\Exception $e) {
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
