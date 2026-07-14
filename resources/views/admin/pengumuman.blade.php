@extends('layouts.app')

@section('title', __('Manajemen Pengumuman'))

@section('content')
<!-- Header Banner Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">LMS Kampus Malang</span>
        <h2 class="text-3xl font-extrabold mt-3 tracking-tight">Manajemen Pengumuman</h2>
        <p class="text-slate-300 text-sm mt-1.5 font-medium">Buat pengumuman penting bagi dosen dan mahasiswa</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ editingPengumuman: null }">
    <!-- Add Form -->
    <div>
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2 border-b pb-3">
                <i class="fas fa-bullhorn text-indigo-500"></i>
                <span>Tambah Pengumuman</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.pengumuman.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Judul Pengumuman</label>
                    <input name="judul" required value="{{ old('judul') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Isi Konten</label>
                    <textarea name="konten" required rows="4" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20">{{ old('konten') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Target Pengguna</label>
                    <select name="target_role" required class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                        <option value="all">Semua Pengguna</option>
                        <option value="dosen">Khusus Dosen</option>
                        <option value="mahasiswa">Khusus Mahasiswa</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 rounded-xl transition-all shadow-md">
                    Publikasikan
                </button>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                            <th class="px-6 py-3.5">Pengumuman</th>
                            <th class="px-6 py-3.5">Target</th>
                            <th class="px-6 py-3.5">Tanggal</th>
                            <th class="px-6 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($pengumumans as $p)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $p->judul }}</div>
                                    <div class="text-xs text-slate-400 mt-1 max-w-sm truncate">{{ $p->konten }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider
                                        {{ $p->target_role === 'all' ? 'bg-indigo-50 text-indigo-600' : '' }}
                                        {{ $p->target_role === 'dosen' ? 'bg-amber-50 text-amber-700' : '' }}
                                        {{ $p->target_role === 'mahasiswa' ? 'bg-emerald-50 text-emerald-700' : '' }}
                                    ">
                                        {{ $p->target_role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-400">
                                    {{ $p->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit"
                                            @click="editingPengumuman = {{ json_encode([
                                                'id' => $p->id,
                                                'judul' => $p->judul,
                                                'konten' => $p->konten,
                                                'target_role' => $p->target_role,
                                                'update_url' => route('admin.pengumuman.update', $p)
                                            ]) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.pengumuman.destroy', $p) }}" onsubmit="return confirm('Hapus pengumuman ini?')" class="m-0">
                                            @csrf @method('DELETE')
                                            <button class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">
                                    Belum ada pengumuman dipublikasikan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Edit Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                 x-show="editingPengumuman !== null" x-cloak>
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-lg w-full p-8" @click.away="editingPengumuman = null">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Form Edit</span>
                            <h4 class="text-lg font-bold text-slate-800 mt-2" x-text="'Ubah: ' + editingPengumuman?.judul"></h4>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600" @click="editingPengumuman = null">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form :action="editingPengumuman?.update_url" method="POST" class="space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Judul Pengumuman</label>
                            <input name="judul" required :value="editingPengumuman?.judul" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Isi Konten</label>
                            <textarea name="konten" required rows="4" :value="editingPengumuman?.konten" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Target Pengguna</label>
                            <select name="target_role" required :value="editingPengumuman?.target_role" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                                <option value="all">Semua Pengguna</option>
                                <option value="dosen">Khusus Dosen</option>
                                <option value="mahasiswa">Khusus Mahasiswa</option>
                            </select>
                        </div>

                        <div class="flex gap-3 justify-end pt-3">
                            <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-4 py-2.5 rounded-xl transition-all" @click="editingPengumuman = null">
                                Batal
                            </button>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition-all shadow-lg">
                                Simpan
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
