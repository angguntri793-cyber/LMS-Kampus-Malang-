<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Prodi;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $query = User::query()->with('prodi', 'kelas');

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
        $prodis = Prodi::orderBy('nama')->get();
        $kelas = Kelas::orderBy('nama')->get();

        return view('admin.users.index', compact('users', 'prodis', 'kelas'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'nomor_induk' => 'required|string|unique:users,nomor_induk',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', Rule::in(['admin', 'dosen', 'mahasiswa'])],
            'password' => 'required|string|min:6',
            'jurusan' => 'nullable|string',
            'telepon' => 'nullable|string',
            'alamat' => 'nullable|string',
            'prodi_id' => 'nullable|exists:prodis,id',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['locale'] = 'id';
        $data['is_active'] = true;

        $user = User::create($data);
        
        Auth::user()->logActivity("Membuat user baru: {$user->name} ({$user->role})");

        return back()->with('success', __('User berhasil ditambahkan.'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $data = $request->validate([
            'nomor_induk' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'dosen', 'mahasiswa'])],
            'password' => 'nullable|string|min:6',
            'jurusan' => 'nullable|string',
            'telepon' => 'nullable|string',
            'alamat' => 'nullable|string',
            'prodi_id' => 'nullable|exists:prodis,id',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        Auth::user()->logActivity("Memperbarui user ID {$user->id}: {$user->name}");

        return back()->with('success', __('User berhasil diperbarui.'));
    }

    public function toggleActive(User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($user->id === auth()->id()) {
            return back()->withErrors([__('Anda tidak dapat menonaktifkan akun Anda sendiri.')]);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'Mengaktifkan' : 'Menonaktifkan';
        Auth::user()->logActivity("{$status} akun user ID {$user->id}: {$user->name}");

        return back()->with('success', __("Status akun {$user->name} berhasil diubah."));
    }

    public function resetPassword(Request $request, User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Auth::user()->logActivity("Mereset password user ID {$user->id}: {$user->name}");

        return back()->with('success', __("Password untuk {$user->name} berhasil direset."));
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        
        if ($user->id === auth()->id()) {
            return back()->withErrors([__('Anda tidak dapat menghapus akun Anda sendiri.')]);
        }

        Auth::user()->logActivity("Menghapus user: {$user->name} ({$user->role})");
        $user->delete();

        return back()->with('success', __('User berhasil dihapus.'));
    }

    // ==========================================
    // IMPORT & EXPORT CSV / PDF
    // ==========================================
    public function exportCsv()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $users = User::orderBy('role')->orderBy('name')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=users_export_' . date('Ymd_His') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use($users) {
            $file = fopen('php://output', 'w');
            // Column Headers
            fputcsv($file, ['Nomor Induk', 'Nama', 'Email', 'Role', 'Jurusan', 'Telepon', 'Alamat', 'Aktif']);

            foreach ($users as $u) {
                fputcsv($file, [
                    $u->nomor_induk,
                    $u->name,
                    $u->email,
                    $u->role,
                    $u->jurusan,
                    $u->telepon,
                    $u->alamat,
                    $u->is_active ? 'Aktif' : 'Nonaktif',
                ]);
            }
            fclose($file);
        };

        Auth::user()->logActivity("Mengekspor data user ke CSV");

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        
        $users = User::with('prodi', 'kelas')->orderBy('role')->orderBy('name')->get();
        Auth::user()->logActivity("Mengekspor/mencetak data user PDF");

        return view('admin.users.pdf', compact('users'));
    }

    public function importCsv(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate([
            'file' => 'required|file|max:2048', // Allow CSV or txt
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return back()->withErrors(['file' => 'Gagal membuka file CSV.']);
        }

        // Get header row
        $header = fgetcsv($handle, 1000, ',');
        
        if ($header === false) {
            fclose($handle);
            return back()->withErrors(['file' => 'File CSV kosong atau tidak memiliki baris header.']);
        }

        // Standardize headers
        $header = array_map(function($h) {
            return strtolower(trim(str_replace(' ', '_', $h)));
        }, $header);

        $count = 0;
        $errors = [];
        
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($header) !== count($row)) {
                // Skip mismatched rows
                continue;
            }

            $data = array_combine($header, $row);

            // Clean inputs
            $nomor_induk = trim($data['nomor_induk'] ?? $data['nim'] ?? $data['nidn'] ?? '');
            $name = trim($data['nama'] ?? $data['name'] ?? '');
            $email = trim($data['email'] ?? '');
            $role = strtolower(trim($data['role'] ?? 'mahasiswa'));
            $password = trim($data['password'] ?? 'password');
            $jurusan = trim($data['jurusan'] ?? '');
            $telepon = trim($data['telepon'] ?? '');
            $alamat = trim($data['alamat'] ?? '');

            if (empty($nomor_induk) || empty($name) || empty($email)) {
                $errors[] = "Baris " . ($count + 2) . ": Nomor induk, nama, dan email wajib diisi.";
                continue;
            }

            // Validate unique constraints manually to avoid crash
            $exists = User::where('nomor_induk', $nomor_induk)->orWhere('email', $email)->exists();
            if ($exists) {
                // Update existing record
                $user = User::where('nomor_induk', $nomor_induk)->first() ?: User::where('email', $email)->first();
                $user->update([
                    'name' => $name,
                    'email' => $email,
                    'role' => in_array($role, ['admin', 'dosen', 'mahasiswa']) ? $role : 'mahasiswa',
                    'jurusan' => $jurusan,
                    'telepon' => $telepon,
                    'alamat' => $alamat,
                ]);
            } else {
                // Create new
                User::create([
                    'nomor_induk' => $nomor_induk,
                    'name' => $name,
                    'email' => $email,
                    'role' => in_array($role, ['admin', 'dosen', 'mahasiswa']) ? $role : 'mahasiswa',
                    'password' => Hash::make($password),
                    'jurusan' => $jurusan,
                    'telepon' => $telepon,
                    'alamat' => $alamat,
                    'is_active' => true,
                    'locale' => 'id',
                ]);
            }
            $count++;
        }

        fclose($handle);

        Auth::user()->logActivity("Mengimpor data user dari CSV");

        if (!empty($errors)) {
            return back()->with('success', "Berhasil memproses {$count} data user dengan beberapa kendala:")
                         ->withErrors($errors);
        }

        return back()->with('success', "Berhasil mengimpor/memperbarui {$count} pengguna dari CSV.");
    }
}
