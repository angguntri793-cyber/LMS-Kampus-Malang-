<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'nomor_induk' => 'required|string|unique:users,nomor_induk',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', Rule::in(['dosen', 'mahasiswa'])],
            'password' => 'required|string|min:6',
            'jurusan' => 'nullable|string',
            'telepon' => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['locale'] = 'id';

        User::create($data);

        return back()->with('success', __('User berhasil ditambahkan.'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'nomor_induk' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['dosen', 'mahasiswa'])],
            'password' => 'nullable|string|min:6',
            'jurusan' => 'nullable|string',
            'telepon' => 'nullable|string',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', __('User berhasil diperbarui.'));
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        
        // Prevent deleting oneself
        if ($user->id === auth()->id()) {
            return back()->withErrors([__('Anda tidak dapat menghapus akun Anda sendiri.')]);
        }

        $user->delete();

        return back()->with('success', __('User berhasil dihapus.'));
    }
}
