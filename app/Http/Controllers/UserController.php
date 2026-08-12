<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('projects')->get();
        
        // Menghitung jumlah per role untuk ringkasan di atas
        $counts = [
            'Admin' => User::where('role', 'Admin')->count(),
            'QA Lead' => User::where('role', 'QA Lead')->count(),
            'QA Tester' => User::where('role', 'QA Tester')->count(),
            'Developer' => User::where('role', 'Developer')->count(),
        ];

        return view('users.index', compact('users', 'counts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:Admin,QA Lead,QA Tester,Developer',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Pengguna berhasil diundang!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:Admin,QA Lead,QA Tester,Developer',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return back()->with('success', 'Pengguna berhasil dihapus!');
    }
}