@extends('layouts.app')

@section('title', __('Manajemen Mata Kuliah'))

@section('content')
<!-- Header Banner Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">LMS Kampus Malang</span>
        <h2 class="text-3xl font-extrabold mt-3 tracking-tight">Manajemen Mata Kuliah</h2>
        <p class="text-slate-300 text-sm mt-1.5 font-medium">Kelola data mata kuliah, dosen pengampu, semester, dan pemetaan kelas</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8" x-data="{ editingCourse: null }">
    <!-- Add Form -->
    <div>
        <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2 border-b pb-3">
                <i class="fas fa-book text-indigo-500"></i>
                <span>Tambah Matkul</span>
            </h3>
            
            <form method="POST" action="{{ route('admin.courses.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Kode Matkul</label>
                    <input name="kode" required value="{{ old('kode') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="e.g. IF-302">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Mata Kuliah</label>
                    <input name="nama_matkul" required value="{{ old('nama_matkul') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="e.g. Pemrograman Web">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Dosen Pengampu</label>
                    <select name="dosen_id" required class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white bg-no-repeat">
                        <option value="">-- Pilih Dosen --</option>
                        @foreach($dosens as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->nomor_induk }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Program Studi</label>
                    <select name="prodi_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                        <option value="">-- Pilih Prodi --</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Semester</label>
                    <select name="semester_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                        <option value="">-- Pilih Semester --</option>
                        @foreach($semesters as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Kelas Sasaran (Enroll Otomatis)</label>
                    <select name="kelas_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 rounded-xl transition-all shadow-md">
                    Simpan Matkul
                </button>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                            <th class="px-6 py-3.5">Kode</th>
                            <th class="px-6 py-3.5">Nama Mata Kuliah</th>
                            <th class="px-6 py-3.5">Dosen Pengampu</th>
                            <th class="px-6 py-3.5">Kelas / Prodi / Semester</th>
                            <th class="px-6 py-3.5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse($courses as $c)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-indigo-600">{{ $c->kode }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 leading-tight">{{ $c->nama_matkul }}</div>
                                    <div class="text-[10px] text-slate-400 mt-1 max-w-xs truncate" title="{{ $c->deskripsi }}">
                                        {{ $c->deskripsi ?: '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ $c->dosen->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-semibold text-slate-800">Kelas: {{ $c->kelas ? $c->kelas->nama : '-' }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold mt-0.5">Prodi: {{ $c->prodi ? $c->prodi->nama : '-' }}</div>
                                    <div class="text-[10px] text-indigo-500 font-bold mt-0.5">{{ $c->semester ? $c->semester->nama : '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.courses.enrollments', $c) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Manage Enrollments">
                                            <i class="fas fa-user-plus"></i>
                                        </a>
                                        <button class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit"
                                            @click="editingCourse = {{ json_encode([
                                                 'id' => $c->id,
                                                 'nama_matkul' => $c->nama_matkul,
                                                 'kode' => $c->kode,
                                                 'dosen_id' => $c->dosen_id,
                                                 'prodi_id' => $c->prodi_id,
                                                 'semester_id' => $c->semester_id,
                                                 'kelas_id' => $c->kelas_id,
                                                 'deskripsi' => $c->deskripsi,
                                                 'update_url' => route('admin.courses.update', $c)
                                             ]) }}">
                                             <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('admin.courses.destroy', $c) }}" onsubmit="return confirm('Hapus matkul {{ $c->nama_matkul }}?')" class="m-0">
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
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">
                                    Belum ada data mata kuliah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Edit Modal -->
            <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
                 x-show="editingCourse !== null" x-cloak>
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-lg w-full p-8" @click.away="editingCourse = null">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Form Edit</span>
                            <h4 class="text-lg font-bold text-slate-800 mt-2" x-text="'Ubah: ' + editingCourse?.nama_matkul"></h4>
                        </div>
                        <button class="text-slate-400 hover:text-slate-600" @click="editingCourse = null">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <form :action="editingCourse?.update_url" method="POST" class="space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Kode Matkul</label>
                            <input name="kode" required :value="editingCourse?.kode" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Mata Kuliah</label>
                            <input name="nama_matkul" required :value="editingCourse?.nama_matkul" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Dosen Pengampu</label>
                            <select name="dosen_id" required :value="editingCourse?.dosen_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($dosens as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Program Studi</label>
                            <select name="prodi_id" :value="editingCourse?.prodi_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Semester</label>
                            <select name="semester_id" :value="editingCourse?.semester_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                                <option value="">-- Pilih Semester --</option>
                                @foreach($semesters as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Kelas</label>
                            <select name="kelas_id" :value="editingCourse?.kelas_id" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" :value="editingCourse?.deskripsi" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none"></textarea>
                        </div>

                        <div class="flex gap-3 justify-end pt-3">
                            <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-4 py-2.5 rounded-xl transition-all" @click="editingCourse = null">
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
