@extends('layouts.app')

@section('title', __('User Management'))

@section('content')
<!-- Header Banner Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
        <div>
            <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">LMS Kampus Malang</span>
            <h2 class="text-3xl font-extrabold mt-3 tracking-tight">{{ __('User Management') }}</h2>
            <p class="text-slate-300 text-sm mt-1.5 font-medium">Kelola akun Dosen, Mahasiswa, dan Administrator Kampus</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.export.csv') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-md flex items-center gap-2">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.users.export.pdf') }}" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-md flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Cetak PDF
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Left column: Add User & Import -->
    <div class="space-y-8">
        <!-- Add User Form -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2 border-b pb-3">
                <i class="fas fa-user-plus text-indigo-500"></i>
                <span>{{ __('Add User') }}</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nomor Induk (NIM/NIP)</label>
                    <input name="nomor_induk" required value="{{ old('nomor_induk') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Lengkap</label>
                    <input name="name" required value="{{ old('name') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Email</label>
                    <input type="email" name="email" required value="{{ old('email') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Role</label>
                    <select name="role" required class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="dosen">Dosen</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Program Studi</label>
                    <select name="prodi_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
                        <option value="">-- Pilih Prodi --</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Telepon</label>
                    <input name="telepon" value="{{ old('telepon') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 rounded-xl transition-all shadow-md shadow-indigo-600/10">
                    Simpan User
                </button>
            </form>
        </div>

        <!-- Import CSV Form -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2 border-b pb-3">
                <i class="fas fa-file-import text-indigo-500"></i>
                <span>Import CSV (Excel)</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.users.import.csv') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">File CSV</label>
                    <input type="file" name="file" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all border border-slate-200 rounded-xl p-1">
                </div>
                <p class="text-[10px] text-slate-400 font-medium">Header CSV wajib: <b>nomor_induk, nama, email, role, password, jurusan, telepon, alamat</b></p>
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white font-bold text-sm py-2.5 rounded-xl transition-all shadow-md">
                    Import
                </button>
            </form>
        </div>
    </div>

    <!-- Right column: Users table list -->
    <div class="lg:col-span-3 space-y-6">
        <!-- Search and filters -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex-1 min-w-[240px]">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-3.5 text-slate-400"></i>
                        <input name="search" value="{{ request('search') }}" placeholder="Cari nama, email, nomor induk..." class="w-full text-sm border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                    </div>
                </div>
                <div class="flex gap-3">
                    <select name="role" class="text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                        <option value="">-- Semua Role --</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    </select>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors flex items-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ editingUser: null, resetUser: null }">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                            <th class="px-6 py-3.5">Identitas</th>
                            <th class="px-6 py-3.5">Kontak</th>
                            <th class="px-6 py-3.5">Akademik</th>
                            <th class="px-6 py-3.5">Role</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $u->avatar_url }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-slate-100">
                                        <div>
                                            <div class="font-bold text-slate-900 leading-tight">{{ $u->name }}</div>
                                            <div class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $u->nomor_induk }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-medium">{{ $u->email }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $u->telepon ?: '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-semibold text-slate-800">{{ $u->prodi ? $u->prodi->nama : $u->jurusan }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold mt-0.5">{{ $u->kelas ? 'Kelas ' . $u->kelas->nama : '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider
                                        {{ $u->role === 'admin' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : '' }}
                                        {{ $u->role === 'dosen' ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                                        {{ $u->role === 'mahasiswa' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}
                                    ">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="m-0">
                                        @csrf
                                        <button class="inline-flex items-center gap-1.5 text-xs font-semibold transition-all rounded-full px-2.5 py-0.5 border
                                            {{ $u->is_active 
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' 
                                                : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200'
                                            }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $u->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                            <span>{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit"
                                            @click="editingUser = {{ json_encode([
                                                'id' => $u->id,
                                                'name' => $u->name,
                                                'nomor_induk' => $u->nomor_induk,
                                                'email' => $u->email,
                                                'role' => $u->role,
                                                'prodi_id' => $u->prodi_id,
                                                'kelas_id' => $u->kelas_id,
                                                'telepon' => $u->telepon,
                                                'alamat' => $u->alamat,
                                                'update_url' => route('admin.users.update', $u)
                                            ]) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition-colors" title="Reset Password"
                                            @click="resetUser = {{ json_encode([
                                                'name' => $u->name,
                                                'reset_url' => route('admin.users.reset-password', $u)
                                            ]) }}">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        @if($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus user {{ $u->name }}?')" class="m-0">
                                                @csrf @method('DELETE')
                                                <button class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">
                                    Belum ada pengguna terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $users->links() }}
                </div>
            @endif

            <!-- Edit User Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                 x-show="editingUser !== null" x-cloak>
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-lg w-full p-8" @click.away="editingUser = null">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Form Edit</span>
                            <h4 class="text-lg font-bold text-slate-800 mt-2" x-text="'Ubah Data: ' + editingUser?.name"></h4>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600" @click="editingUser = null">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form :action="editingUser?.update_url" method="POST" class="space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Nomor Induk (NIM/NIP)</label>
                            <input name="nomor_induk" required :value="editingUser?.nomor_induk" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Lengkap</label>
                            <input name="name" required :value="editingUser?.name" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Email</label>
                            <input type="email" name="email" required :value="editingUser?.email" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Password Baru (kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none" placeholder="Kosongkan...">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Role</label>
                            <select name="role" required :value="editingUser?.role" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="dosen">Dosen</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Program Studi</label>
                            <select name="prodi_id" :value="editingUser?.prodi_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Kelas</label>
                            <select name="kelas_id" :value="editingUser?.kelas_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Telepon</label>
                            <input name="telepon" :value="editingUser?.telepon" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Alamat</label>
                            <textarea name="alamat" rows="2" :value="editingUser?.alamat" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none"></textarea>
                        </div>

                        <div class="flex gap-3 justify-end pt-3">
                            <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-5 py-3 rounded-xl transition-all" @click="editingUser = null">
                                Batal
                            </button>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all shadow-lg">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reset Password Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                 x-show="resetUser !== null" x-cloak>
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-sm w-full p-8" @click.away="resetUser = null">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="bg-rose-50 text-rose-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Reset Keamanan</span>
                            <h4 class="text-lg font-bold text-slate-800 mt-2" x-text="'Reset Sandi: ' + resetUser?.name"></h4>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600" @click="resetUser = null">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form :action="resetUser?.reset_url" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Password Baru</label>
                            <input type="password" name="password" required class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>

                        <div class="flex gap-3 justify-end pt-3">
                            <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-4 py-2.5 rounded-xl transition-all" @click="resetUser = null">
                                Batal
                            </button>
                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-lg">
                                Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
