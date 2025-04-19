<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Menampilkan form login (jika diperlukan).
     */
    public function showLoginForm()
    {
        // Cek jika user sudah login
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('login.form');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Jika login berhasil
            session()->flash('success', 'Login successful! Welcome to W Mart.');
            return redirect()->intended('/dashboard');
        }

        // Jika login gagal
        return redirect()->back()->withErrors(['email' => 'Incorrect email or password.']);
    }
    
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
