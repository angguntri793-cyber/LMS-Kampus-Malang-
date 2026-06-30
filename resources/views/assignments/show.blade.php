@extends('layouts.app')

@section('title', $assignment->judul)

@section('content')
<!-- Back navigation -->
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('courses.show', $assignment->course) }}" class="inline-flex items-center text-slate-500 hover:text-slate-800 text-sm font-semibold transition-colors gap-1.5 font-medium">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>{{ __('Kembali ke Mata Kuliah') }}</span>
    </a>
</div>

<!-- Assignment Details -->
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $assignment->judul }}</h2>
    <div class="text-xs text-slate-400 font-semibold mt-1 uppercase tracking-wider flex items-center gap-1.5">
        <i class="fas fa-book-bookmark"></i>
        <span>{{ $assignment->course->nama_matkul }}</span>
    </div>
    
    <p class="text-sm text-slate-600 mt-4 leading-relaxed whitespace-pre-line bg-slate-50/50 rounded-xl p-4 border border-slate-100">
        {{ $assignment->deskripsi ?: __('Tidak ada deskripsi.') }}
    </p>
    
    <div class="mt-5 flex flex-wrap items-center gap-3 text-xs font-semibold">
        <span class="inline-flex bg-rose-50 border border-rose-100 text-rose-700 px-3 py-1.5 rounded-xl items-center gap-1.5">
            <i class="far fa-calendar-xmark text-rose-500"></i>
            {{ __('Deadline') }}: {{ $assignment->deadline->format('l, d F Y, H:i') }}
        </span>
        <span class="inline-flex bg-slate-100 text-slate-600 px-3 py-1.5 rounded-xl items-center gap-1.5">
            <i class="far fa-file-lines text-slate-500"></i>
            {{ __('Format') }}: {{ $assignment->allowed_types }}
        </span>
        <span class="inline-flex bg-slate-100 text-slate-600 px-3 py-1.5 rounded-xl items-center gap-1.5">
            <i class="fas fa-weight-hanging text-slate-500"></i>
            {{ __('Maks') }}: {{ $assignment->max_size_mb }} MB
        </span>
    </div>
</div>

@if(auth()->user()->isMahasiswa())
    <!-- STUDENT SUBMISSION PANEL (MOODLE-STYLE TABLE) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <div class="bg-slate-900 text-white px-6 py-4">
            <h3 class="font-bold text-sm tracking-wide uppercase">{{ __('Submission status') }}</h3>
        </div>

        <div class="divide-y divide-slate-100 text-sm">
            <!-- Submission status -->
            <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                <div class="font-bold text-slate-500">{{ __('Submission Status') }}</div>
                <div class="md:col-span-2">
                    @if($mySubmission && $mySubmission->submitted_at)
                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full">
                            {{ __('Submitted for grading') }}
                        </span>
                    @else
                        <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1 rounded-full">
                            {{ __('No attempt') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Grading status -->
            <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                <div class="font-bold text-slate-500">{{ __('Grading Status') }}</div>
                <div class="md:col-span-2">
                    @if($mySubmission && $mySubmission->nilai !== null)
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">
                            {{ __('Graded') }}
                        </span>
                    @else
                        <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1 rounded-full">
                            {{ __('Not graded') }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Time remaining -->
            <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                <div class="font-bold text-slate-500">{{ __('Time Remaining') }}</div>
                <div class="md:col-span-2">
                    @if($mySubmission && $mySubmission->submitted_at)
                        @php $st = $mySubmission->deadlineStatus(); @endphp
                        <span class="font-semibold {{ $st['class'] }}">{{ $st['text'] }}</span>
                    @else
                        @php
                            $diff = now()->diff($assignment->deadline);
                            $isLate = now()->gt($assignment->deadline);
                        @endphp
                        @if($isLate)
                            <span class="text-rose-600 font-semibold">{{ __('Tugas terlambat dikumpulkan') }}</span>
                        @else
                            <span class="text-slate-600 font-semibold">
                                {{ $diff->d }} {{ __('hari') }} {{ $diff->h }} {{ __('jam') }} {{ __('tersisa') }}
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Last modified -->
            <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                <div class="font-bold text-slate-500">{{ __('Last Modified') }}</div>
                <div class="md:col-span-2 font-medium">
                    {{ $mySubmission ? $mySubmission->updated_at->format('l, d F Y, H:i') : '-' }}
                </div>
            </div>

            <!-- File submissions -->
            <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                <div class="font-bold text-slate-500">{{ __('File Submissions') }}</div>
                <div class="md:col-span-2">
                    @if($mySubmission && $mySubmission->file_path)
                        <div class="flex items-center gap-3">
                            <i class="far fa-file-pdf text-rose-500 text-xl"></i>
                            <div class="min-w-0">
                                <a href="{{ route('submissions.download', $mySubmission) }}" class="font-bold text-indigo-600 hover:text-indigo-700 hover:underline truncate block text-sm">
                                    {{ $mySubmission->file_name }}
                                </a>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $mySubmission->submitted_at->format('d F Y, H:i') }}</span>
                            </div>
                        </div>
                    @else
                        <span class="text-slate-400 font-medium">-</span>
                    @endif
                </div>
            </div>

            <!-- Student Notes / Comments -->
            <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                <div class="font-bold text-slate-500">{{ __('Student Notes') }}</div>
                <div class="md:col-span-2 font-medium text-slate-600 italic">
                    {{ ($mySubmission && $mySubmission->catatan) ? $mySubmission->catatan : '-' }}
                </div>
            </div>

            <!-- Mark Complete status -->
            <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                <div class="font-bold text-slate-500">{{ __('Mark as Complete') }}</div>
                <div class="md:col-span-2">
                    @if($mySubmission)
                        @if($mySubmission->is_completed)
                            <div class="flex items-center gap-1.5 text-emerald-600 font-bold text-xs uppercase">
                                <i class="fas fa-circle-check"></i>
                                <span>{{ __('Completed') }}</span>
                            </div>
                        @else
                            <form method="POST" action="{{ route('submissions.complete', $mySubmission) }}" class="m-0">
                                @csrf
                                <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl transition-all shadow-sm shadow-emerald-600/10">
                                    {{ __('Mark as Complete') }}
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="text-slate-400 text-xs font-semibold">{{ __('Unggah file terlebih dahulu') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- FEEDBACK SECTION IF GRADED -->
    @if($mySubmission && $mySubmission->nilai !== null)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
            <div class="bg-indigo-900 text-white px-6 py-4">
                <h3 class="font-bold text-sm tracking-wide uppercase">{{ __('Feedback') }}</h3>
            </div>
            
            <div class="divide-y divide-slate-100 text-sm">
                <!-- Grade -->
                <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                    <div class="font-bold text-slate-500">{{ __('Grade') }}</div>
                    <div class="md:col-span-2 text-lg font-bold text-indigo-700">
                        {{ number_format($mySubmission->nilai, 2) }} / 100.00
                    </div>
                </div>

                <!-- Graded on -->
                <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                    <div class="font-bold text-slate-500">{{ __('Graded on') }}</div>
                    <div class="md:col-span-2 font-medium text-slate-600">
                        {{ $mySubmission->updated_at->format('l, d F Y, H:i') }}
                    </div>
                </div>

                <!-- Graded by -->
                <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                    <div class="font-bold text-slate-500">{{ __('Graded by') }}</div>
                    <div class="md:col-span-2 font-bold text-slate-800">
                        {{ $assignment->course->dosen->name }} <span class="bg-slate-100 text-slate-500 text-xs font-semibold px-2 py-0.5 rounded ml-1">{{ $assignment->course->dosen->nomor_induk }}</span>
                    </div>
                </div>

                <!-- Feedback Comments -->
                <div class="grid grid-cols-1 md:grid-cols-3 px-6 py-4">
                    <div class="font-bold text-slate-500">{{ __('Lecturer Feedback') }}</div>
                    <div class="md:col-span-2 font-medium text-slate-700 whitespace-pre-line bg-slate-50 rounded-xl p-4 border border-slate-100">
                        {{ $mySubmission->feedback ?: __('Tidak ada catatan feedback.') }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- SUBMIT FORM -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center gap-2">
            <i class="fas fa-arrow-up-from-bracket text-indigo-500"></i>
            <span>{{ $mySubmission ? __('Upload Ulang Tugas') : __('Unggah File Tugas') }}</span>
        </h3>
        
        <form method="POST" action="{{ route('submissions.store', $assignment) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">{{ __('Pilih File') }}</label>
                <input type="file" name="file" required 
                    class="block w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-slate-50 hover:bg-slate-100 file:mr-4 file:py-2.5 file:px-5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white file:cursor-pointer hover:file:bg-indigo-700 transition-all">
                <p class="text-[10px] text-slate-400 mt-2 font-semibold uppercase tracking-wider">
                    {{ __('Format diizinkan') }}: {{ $assignment->allowed_types }} • {{ __('Maksimal') }} {{ $assignment->max_size_mb }} MB
                </p>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">{{ __('Catatan Anda untuk Dosen') }}</label>
                <textarea name="catatan" rows="3" placeholder="{{ __('Ketik komentar atau catatan pendukung...') }}" 
                    class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-all bg-slate-50/50"></textarea>
            </div>

            <button class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all duration-200 shadow-lg shadow-indigo-600/25 gap-2">
                <i class="fas fa-cloud-arrow-up"></i>
                <span>{{ $mySubmission ? __('Unggah Ulang') : __('Kirim Tugas') }}</span>
            </button>
        </form>
    </div>

@else
    <!-- LECTURER / ADMIN VIEW: LIST OF SUBMISSIONS -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8" x-data="{activeSubmission: null}">
        <div class="bg-slate-900 text-white px-6 py-4 flex justify-between items-center">
            <h3 class="font-bold text-sm tracking-wide uppercase">Pengumpulan Mahasiswa</h3>
            <span class="bg-slate-800 text-slate-300 text-xs font-semibold px-2.5 py-1 rounded-lg border border-slate-700">
                {{ $assignment->submissions->count() }} Pengumpulan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                        <th class="px-6 py-3.5">NIM</th>
                        <th class="px-6 py-3.5">Nama</th>
                        <th class="px-6 py-3.5">File</th>
                        <th class="px-6 py-3.5">Status Waktu</th>
                        <th class="px-6 py-3.5">Catatan</th>
                        <th class="px-6 py-3.5">Nilai</th>
                        <th class="px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($assignment->submissions as $s)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $s->user->nomor_induk }}</td>
                            <td class="px-6 py-4 font-medium">{{ $s->user->name }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('submissions.download', $s) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 hover:underline gap-1.5 font-bold">
                                    <i class="far fa-file"></i>
                                    <span>{{ $s->file_name }}</span>
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @php $st = $s->deadlineStatus(); @endphp
                                <span class="font-semibold text-xs {{ $st['class'] }}">{{ $st['text'] }}</span>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate text-slate-500 italic" title="{{ $s->catatan }}">
                                {{ $s->catatan ?: '-' }}
                            </td>
                            <td class="px-6 py-4 font-bold">
                                @if($s->nilai !== null)
                                    <span class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded">{{ $s->nilai }}</span>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-3.5 py-2 rounded-xl transition-all shadow-sm"
                                    @click="activeSubmission = {{ json_encode([
                                        'id' => $s->id,
                                        'name' => $s->user->name,
                                        'nilai' => $s->nilai,
                                        'feedback' => $s->feedback,
                                        'file_name' => $s->file_name,
                                        'grade_url' => route('submissions.grade', $s)
                                    ]) }}">
                                    <i class="fas fa-star mr-1"></i> Nilai
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium">
                                Belum ada mahasiswa yang mengumpulkan tugas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Inline Grading Overlay Form Modal via AlpineJS -->
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
             x-show="activeSubmission !== null" x-cloak>
            <div class="bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-lg w-full p-8" @click.away="activeSubmission = null">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">Form Penilaian</span>
                        <h4 class="text-lg font-bold text-slate-800 mt-2" x-text="'Beri Nilai: ' + activeSubmission?.name"></h4>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="'File: ' + activeSubmission?.file_name"></p>
                    </div>
                    <button class="text-slate-400 hover:text-slate-600" @click="activeSubmission = null">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <form :action="activeSubmission?.grade_url" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Nilai Tugas (0 - 100)</label>
                        <input type="number" name="nilai" min="0" max="100" required :value="activeSubmission?.nilai"
                            class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Feedback Dosen</label>
                        <textarea name="feedback" rows="4" placeholder="Tulis catatan atau saran perbaikan tugas..." :value="activeSubmission?.feedback"
                            class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-all"></textarea>
                    </div>

                    <div class="flex gap-3 justify-end pt-3">
                        <button type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs px-5 py-3 rounded-xl transition-all" @click="activeSubmission = null">
                            Batal
                        </button>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-5 py-3 rounded-xl transition-all shadow-lg shadow-indigo-600/25">
                            Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection