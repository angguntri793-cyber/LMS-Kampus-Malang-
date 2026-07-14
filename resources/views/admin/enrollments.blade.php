@extends('layouts.app')

@section('title', __('Manajemen Enrollment') . ' - ' . $course->nama_matkul)

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-800 text-sm font-semibold transition-colors gap-1.5 font-medium">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>{{ __('Kembali ke Daftar Mata Kuliah') }}</span>
    </a>
</div>

<!-- Course Info Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-905 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-600/60 text-indigo-100 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">{{ $course->kode }}</span>
        <h2 class="text-2xl font-extrabold mt-3 tracking-tight">Manajemen Enrollment Mahasiswa</h2>
        <p class="text-slate-300 text-sm mt-1.5">Mata Kuliah: <b>{{ $course->nama_matkul }}</b> • Dosen: <b>{{ $course->dosen->name }}</b></p>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm mb-6">
    <form method="GET" action="{{ route('admin.courses.enrollments', $course) }}" class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex-1 min-w-[240px]">
            <div class="relative">
                <i class="fas fa-search absolute left-4 top-3.5 text-slate-400"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIM mahasiswa..." class="w-full text-sm border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>
        </div>
        <div class="flex gap-3">
            <select name="kelas_id" class="text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                <option value="">-- Semua Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'kelas_id']))
                <a href="{{ route('admin.courses.enrollments', $course) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors flex items-center">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Enrollment Selection Form -->
<form method="POST" action="{{ route('admin.courses.enrollments.store', $course) }}">
    @csrf
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div class="flex items-center gap-3">
                <input type="checkbox" id="select-all-checkbox" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                <label for="select-all-checkbox" class="text-sm font-bold text-slate-800 cursor-pointer select-none">Pilih Semua di Halaman Ini</label>
            </div>
            <span class="text-xs text-slate-400 font-semibold">Menampilkan {{ $students->count() }} Mahasiswa</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                        <th class="px-6 py-3.5 w-12">Pilih</th>
                        <th class="px-6 py-3.5">Nama & NIM</th>
                        <th class="px-6 py-3.5">Program Studi</th>
                        <th class="px-6 py-3.5">Kelas</th>
                        <th class="px-6 py-3.5">Status Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <!-- Maintain enrolled ids not visible in current pagination via hidden fields -->
                    @foreach($enrolledIds as $id)
                        @if(!$students->pluck('id')->contains($id))
                            <input type="hidden" name="student_ids[]" value="{{ $id }}">
                        @endif
                    @endforeach

                    @forelse($students as $student)
                        @php $isEnrolled = in_array($student->id, $enrolledIds); @endphp
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" {{ $isEnrolled ? 'checked' : '' }} class="student-checkbox w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 leading-tight">{{ $student->name }}</div>
                                <div class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $student->nomor_induk }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-600">{{ $student->prodi ? $student->prodi->nama : $student->jurusan }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                @if($student->kelas)
                                    <span class="bg-slate-100 text-slate-700 text-xs px-2.5 py-0.5 rounded-md font-semibold">{{ $student->kelas->nama }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($isEnrolled)
                                    <span class="inline-flex text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-md uppercase tracking-wider">Terdaftar</span>
                                @else
                                    <span class="inline-flex text-[10px] font-bold px-2 py-0.5 bg-slate-50 text-slate-400 border border-slate-100 rounded-md uppercase tracking-wider">Belum Terdaftar</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">
                                Tidak ada mahasiswa aktif ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $students->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Submit block -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.courses.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-sm px-6 py-3 rounded-xl transition-all">
            Batal
        </a>
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-3 rounded-xl transition-all shadow-lg shadow-indigo-600/20">
            Simpan Perubahan Enrollment
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all-checkbox');
        const checkboxes = document.querySelectorAll('.student-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endsection
