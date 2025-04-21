<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3|confirmed',
            'role' => 'required|in:admin,staff',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.create')
                            ->withErrors($validator)
                            ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('user.index')->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id); // Menemukan user berdasarkan ID
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:3|confirmed',
            'role' => 'required|in:admin,staff',
        ]);

        if ($validator->fails()) {
            return redirect()->route('user.edit', $id)
                            ->withErrors($validator)
                            ->withInput();
        }

        $user = User::findOrFail($id);

        // Periksa apakah password baru sama dengan password lama
        if ($request->filled('password') && Hash::check($request->password, $user->password)) {
            // Jika password baru sama dengan yang lama, kembalikan error
            return redirect()->route('user.edit', $id)
                            ->withErrors(['password' => 'New password cannot be the same as the current password.'])
                            ->withInput();
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            // Hanya perbarui password jika diisi dan tidak sama dengan yang lama
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index')->with('success', 'User deleted successfully!');
    }
}
