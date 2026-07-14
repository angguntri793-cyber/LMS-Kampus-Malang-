@extends('layouts.app')

@section('title', __('Daftar Pengerjaan') . ' - ' . $quiz->judul)

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('courses.show', $quiz->course) }}" class="inline-flex items-center text-slate-500 hover:text-slate-800 text-sm font-semibold transition-colors gap-1.5 font-medium">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>{{ __('Kembali ke Mata Kuliah') }}</span>
    </a>
</div>

<!-- Header Banner Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">{{ $quiz->course->kode }} • Kuis</span>
        <h2 class="text-3xl font-extrabold mt-3 tracking-tight">Daftar Pengerjaan Kuis</h2>
        <p class="text-slate-300 text-sm mt-1.5 font-medium">Kuis: <b>{{ $quiz->judul }}</b></p>
    </div>
</div>

<!-- Attempts List Table -->
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Hasil Pengerjaan Mahasiswa</h3>
        <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-lg border border-indigo-100">
            {{ $attempts->count() }} Pengerjaan
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border-collapse text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                    <th class="px-6 py-3.5">NIM</th>
                    <th class="px-6 py-3.5">Nama Mahasiswa</th>
                    <th class="px-6 py-3.5">Waktu Submit</th>
                    <th class="px-6 py-3.5">Skor Kuis</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($attempts as $attempt)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $attempt->user->nomor_induk }}</td>
                        <td class="px-6 py-4 font-medium">{{ $attempt->user->name }}</td>
                        <td class="px-6 py-4 font-medium text-slate-500">
                            {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center font-bold px-3 py-1 rounded-lg border text-sm
                                {{ $attempt->score >= 75 
                                    ? 'bg-emerald-50 text-emerald-700 border-emerald-100' 
                                    : 'bg-rose-50 text-rose-700 border-rose-100' 
                                }}">
                                {{ $attempt->score }} / 100
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">
                            Belum ada mahasiswa yang mengerjakan kuis ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
