@extends('layouts.app')

@section('title', $quiz->judul)

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('courses.show', $quiz->course) }}" class="inline-flex items-center text-slate-500 hover:text-slate-800 text-sm font-semibold transition-colors gap-1.5 font-medium">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>{{ __('Kembali ke Mata Kuliah') }}</span>
    </a>
</div>

<!-- Main Section -->
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Quiz Meta Info -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 relative overflow-hidden">
        <div class="flex items-start justify-between gap-4">
            <div>
                <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">{{ $quiz->course->kode }} • Kuis</span>
                <h2 class="text-xl font-bold text-slate-800 mt-2">{{ $quiz->judul }}</h2>
                <div class="text-xs text-slate-400 mt-2 flex flex-wrap gap-4 font-semibold">
                    <span class="flex items-center gap-1"><i class="far fa-clock"></i> Durasi: {{ $quiz->durasi_menit }} Menit</span>
                    <span class="flex items-center gap-1"><i class="far fa-calendar-xmark"></i> Batas Waktu: {{ $quiz->deadline->format('d M Y, H:i') }}</span>
                </div>
            </div>
            
            @if(auth()->user()->isDosen())
                <div class="flex gap-2">
                    <a href="{{ route('quizzes.edit', $quiz) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-3 py-2 rounded-xl transition-all">
                        Edit Kuis
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if(auth()->user()->isMahasiswa())
        @if($attempt)
            <!-- Attempt Details Screen (RESULT SCREEN) -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-indigo-900 text-white p-8 text-center relative overflow-hidden">
                    <div class="absolute w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl -top-10 -right-10"></div>
                    <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">Hasil Pengerjaan Kuis</span>
                    <h3 class="text-4xl font-extrabold mt-4 tracking-tight">{{ $attempt->score }} <span class="text-lg font-medium text-indigo-300">/ 100</span></h3>
                    <p class="text-indigo-200 text-xs mt-3 font-semibold">
                        Selesai pada {{ $attempt->submitted_at->format('l, d F Y, H:i') }}
                    </p>
                </div>

                <div class="p-6 space-y-6">
                    <h4 class="font-bold text-slate-800 text-sm border-b pb-3"><i class="fas fa-circle-question text-indigo-500 mr-1.5"></i> Tinjau Jawaban</h4>
                    
                    @php $answers = json_decode($attempt->answers_json, true) ?: []; @endphp
                    <div class="space-y-6">
                        @foreach($questions as $index => $q)
                            @php 
                                $studentAns = $answers[$index] ?? null;
                                $correctAns = $q['answer'];
                                $isCorrect = (strtoupper($studentAns) === strtoupper($correctAns));
                            @endphp
                            <div class="border rounded-2xl p-4 {{ $isCorrect ? 'bg-emerald-50/20 border-emerald-100' : 'bg-rose-50/20 border-rose-100' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="font-bold text-slate-800 text-xs leading-normal">Pertanyaan {{ $index + 1 }}:</span>
                                    @if($isCorrect)
                                        <span class="text-xs font-bold text-emerald-600 flex items-center gap-1"><i class="fas fa-check-circle"></i> Benar</span>
                                    @else
                                        <span class="text-xs font-bold text-rose-600 flex items-center gap-1"><i class="fas fa-times-circle"></i> Salah</span>
                                    @endif
                                </div>
                                <p class="text-xs font-semibold text-slate-800 mt-2 leading-relaxed">{{ $q['question'] }}</p>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 mt-4">
                                    @foreach($q['options'] as $key => $val)
                                        @php
                                            $borderClass = 'border-slate-100 text-slate-600 bg-white';
                                            if (strtoupper($key) === strtoupper($correctAns)) {
                                                $borderClass = 'border-emerald-500 text-emerald-800 bg-emerald-100/50 font-bold';
                                            } elseif (strtoupper($key) === strtoupper($studentAns) && !$isCorrect) {
                                                $borderClass = 'border-rose-400 text-rose-800 bg-rose-100/50 font-bold';
                                            }
                                        @endphp
                                        <div class="border text-[11px] rounded-xl px-3.5 py-2.5 flex items-center gap-2 {{ $borderClass }}">
                                            <span class="w-5 h-5 rounded-full bg-slate-200/50 text-slate-700 font-bold text-[9px] flex items-center justify-center border border-slate-300/40">{{ $key }}</span>
                                            <span>{{ $val }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- Intro / Interactive Exam Panel using AlpineJS -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8" 
                 x-data="{
                     started: false,
                     timeLeft: {{ $quiz->durasi_menit * 60 }},
                     timer: null,
                     init() {
                         // timer init
                     },
                     startQuiz() {
                         this.started = true;
                         this.timer = setInterval(() => {
                             if (this.timeLeft > 0) {
                                 this.timeLeft--;
                             } else {
                                 clearInterval(this.timer);
                                 document.getElementById('quiz-form').submit();
                             }
                         }, 1000);
                     },
                     formatTime() {
                         const m = Math.floor(this.timeLeft / 60);
                         const s = this.timeLeft % 60;
                         return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                     }
                 }">
                
                <!-- Start Quiz Screen -->
                <div x-show="!started" class="text-center py-6 space-y-5">
                    <div class="inline-flex bg-indigo-50 text-indigo-600 p-5 rounded-3xl shadow-sm mb-2 ring-8 ring-indigo-500/5">
                        <i class="fas fa-file-pen text-3xl"></i>
                    </div>
                    <h3 class="font-extrabold text-slate-800 text-lg">Apakah Anda siap memulai kuis?</h3>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                        Setelah Anda menekan tombol "Mulai Kuis", waktu pengerjaan selama <b>{{ $quiz->durasi_menit }} Menit</b> akan segera berjalan. Pastikan koneksi internet stabil.
                    </p>
                    <button @click="startQuiz()" 
                        class="bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-805 text-white font-bold text-xs px-6 py-3 rounded-xl transition-all shadow-md">
                        Mulai Kuis Sekarang
                    </button>
                </div>

                <!-- Exam Screen -->
                <div x-show="started" x-cloak class="space-y-6">
                    <!-- Fixed Timer Widget -->
                    <div class="bg-indigo-900 text-white rounded-2xl px-5 py-3 flex items-center justify-between shadow-md">
                        <div class="flex items-center gap-2">
                            <i class="far fa-clock text-indigo-300 animate-pulse"></i>
                            <span class="text-xs font-semibold">Sisa Waktu Pengerjaan</span>
                        </div>
                        <span class="font-mono font-bold text-lg" x-text="formatTime()"></span>
                    </div>

                    <!-- Questions Form -->
                    <form id="quiz-form" method="POST" action="{{ route('quizzes.submit', $quiz) }}" class="space-y-8">
                        @csrf
                        @foreach($questions as $index => $q)
                            <div class="border border-slate-100 rounded-2xl p-5 bg-slate-50/30">
                                <div class="font-bold text-slate-800 text-xs uppercase tracking-wider mb-2">Pertanyaan {{ $index + 1 }}</div>
                                <p class="text-xs font-semibold text-slate-800 leading-relaxed">{{ $q['question'] }}</p>
                                
                                <div class="space-y-2 mt-4">
                                    @foreach($q['options'] as $key => $val)
                                        <label class="border border-slate-200/60 hover:border-indigo-300 rounded-xl px-4 py-3 flex items-center gap-3 cursor-pointer bg-white text-xs text-slate-650 hover:bg-indigo-50/10 transition-all font-medium">
                                            <input type="radio" name="answers[{{ $index }}]" value="{{ $key }}" required class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                            <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-700 font-bold text-[9px] flex items-center justify-center border border-slate-200">{{ $key }}</span>
                                            <span>{{ $val }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3.5 rounded-xl transition-all shadow-md">
                            Kirim Jawaban Kuis
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
