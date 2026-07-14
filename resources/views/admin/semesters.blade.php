@extends('layouts.app')

@section('title', __('Manajemen Semester'))

@section('content')
<!-- Header Banner Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">LMS Kampus Malang</span>
        <h2 class="text-3xl font-extrabold mt-3 tracking-tight">Manajemen Semester</h2>
        <p class="text-slate-300 text-sm mt-1.5 font-medium">Kelola data semester perkuliahan dan atur status semester aktif</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ editingSemester: null }">
    <!-- Add Form -->
    <div>
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2 border-b pb-3">
                <i class="fas fa-calendar-alt text-indigo-500"></i>
                <span>Tambah Semester</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.semesters.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Semester (e.g. Ganjil 2025/2026)</label>
                    <input name="nama" required value="{{ old('nama') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="e.g. Ganjil 2025/2026">
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 rounded-xl transition-all shadow-md shadow-indigo-600/10">
                    Simpan Semester
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
                            <th class="px-6 py-3.5">Nama Semester</th>
                            <th class="px-6 py-3.5">Status Aktif</th>
                            <th class="px-6 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($semesters as $s)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $s->nama }}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.semesters.toggle', $s) }}" class="m-0">
                                        @csrf
                                        <button class="inline-flex items-center gap-1.5 text-xs font-semibold transition-all rounded-full px-3 py-0.5 border
                                            {{ $s->is_active 
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' 
                                                : 'bg-slate-50 text-slate-500 border-slate-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200'
                                            }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $s->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            <span>{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <button class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit"
                                            @click="editingSemester = {{ json_encode([
                                                'id' => $s->id,
                                                'nama' => $s->nama,
                                                'update_url' => route('admin.semesters.update', $s)
                                            ]) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.semesters.destroy', $s) }}" onsubmit="return confirm('Hapus semester {{ $s->nama }}?')" class="m-0">
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
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400 font-medium">
                                    Belum ada data semester.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Edit Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                 x-show="editingSemester !== null" x-cloak>
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-sm w-full p-8" @click.away="editingSemester = null">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Form Edit</span>
                            <h4 class="text-lg font-bold text-slate-800 mt-2" x-text="'Ubah: ' + editingSemester?.nama"></h4>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600" @click="editingSemester = null">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form :action="editingSemester?.update_url" method="POST" class="space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Semester</label>
                            <input name="nama" required :value="editingSemester?.nama" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>

                        <div class="flex gap-3 justify-end pt-3">
                            <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-4 py-2.5 rounded-xl transition-all" @click="editingSemester = null">
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
