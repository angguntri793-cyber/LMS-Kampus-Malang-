<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller {
    public function edit() {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request) {
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'jurusan' => 'nullable',
            'telepon' => 'nullable',
            'bio' => 'nullable',
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars','public');
        }
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return back()->with('success', __('Profil berhasil diperbarui.'));
    }
}