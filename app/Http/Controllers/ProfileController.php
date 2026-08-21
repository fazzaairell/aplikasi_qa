<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Update nama & email.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->fill($validated);
        $user->save();

        return back()->with('status', 'profile-updated');
    }

    /**
     * Update foto profile — simpan langsung ke public/uploads/profile-photos/
     * agar tidak perlu storage symlink (solusi Windows-safe)
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $user = $request->user();

        // Hapus foto lama jika ada
        if ($user->photo_path) {
            $oldFile = public_path('uploads/' . $user->photo_path);
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
        }

        // Simpan file baru ke public/uploads/profile-photos/
        $file      = $request->file('photo');
        $filename  = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $directory = public_path('uploads/profile-photos');

        // Buat direktori jika belum ada
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file->move($directory, $filename);

        // Simpan path relatif saja (dari uploads/)
        $relativePath = 'profile-photos/' . $filename;
        $user->update(['photo_path' => $relativePath]);

        return back()->with('status', 'photo-updated');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'          => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}