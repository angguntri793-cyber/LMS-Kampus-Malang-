@extends('layouts.app')

@section('title', __('Buat Kuis Baru'))

@section('content')
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('courses.show', $course) }}" class="inline-flex items-center text-slate-500 hover:text-slate-800 text-sm font-semibold transition-colors gap-1.5 font-medium">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>{{ __('Kembali ke Mata Kuliah') }}</span>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight mb-6">Buat Kuis Baru</h2>
            
            <form method="POST" action="{{ route('quizzes.store', $course) }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Judul Kuis</label>
                    <input name="judul" required value="{{ old('judul') }}" placeholder="e.g. Kuis 1: Konsep Dasar AI" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/20">
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Durasi (Menit)</label>
                        <input type="number" name="durasi_menit" required value="{{ old('durasi_menit', 30) }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Batas Waktu (Deadline)</label>
                        <input type="datetime-local" name="deadline" required value="{{ old('deadline') }}" class="w-full text-sm border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-indigo-500/20">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide">Daftar Pertanyaan (Format JSON)</label>
                        <button type="button" onclick="loadTemplate()" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                            Muat Template Contoh
                        </button>
                    </div>
                    <textarea name="questions_json" id="questions_json" required rows="12" class="w-full font-mono text-xs border border-slate-200 rounded-xl p-4 outline-none focus:ring-2 focus:ring-indigo-500/20" placeholder="Salin template contoh pertanyaan di sini..."></textarea>
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-3.5 rounded-xl transition-all shadow-lg shadow-indigo-600/20">
                    Buat Kuis
                </button>
            </form>
        </div>
    </div>

    <!-- Instructions / JSON template reference -->
    <div>
        <div class="bg-slate-900 text-slate-300 rounded-3xl p-6 shadow-sm space-y-4">
            <h4 class="font-bold text-white text-sm uppercase tracking-wider">Petunjuk Pembuatan Kuis</h4>
            <p class="text-xs text-slate-400 leading-relaxed">
                Kuis di LMS menggunakan format JSON untuk mendefinisikan pertanyaan pilihan ganda. Sistem akan mem-parsing pertanyaan secara otomatis dan mengoreksi jawaban mahasiswa.
            </p>
            <div class="border-t border-slate-800 pt-4">
                <h5 class="text-xs font-bold text-white mb-2 uppercase">Struktur Pertanyaan:</h5>
                <pre class="bg-slate-950 p-3 rounded-xl overflow-x-auto text-[10px] font-mono leading-relaxed text-indigo-300">
[
  {
    "question": "Pertanyaan nomor 1?",
    "options": {
      "A": "Pilihan A",
      "B": "Pilihan B",
      "C": "Pilihan C",
      "D": "Pilihan D"
    },
    "answer": "A"
  }
]</pre>
            </div>
            <div class="text-[11px] text-amber-400 font-semibold bg-amber-950/20 border border-amber-900/50 p-3.5 rounded-xl leading-relaxed">
                <i class="fas fa-exclamation-triangle mr-1"></i> Kunci jawaban (<code>answer</code>) harus menggunakan huruf kapital tunggal (A, B, C, atau D).
            </div>
        </div>
    </div>
</div>

<script>
    function loadTemplate() {
        const template = [
            {
                "question": "Manakah dari berikut ini yang merupakan contoh algoritma pencarian heuristic?",
                "options": {
                    "A": "Breadth-First Search (BFS)",
                    "B": "Depth-First Search (DFS)",
                    "C": "A* Search",
                    "D": "Dijkstra Search"
                },
                "answer": "C"
            },
            {
                "question": "Siapakah ilmuwan komputer yang mengusulkan konsep Turing Test?",
                "options": {
                    "A": "Alan Turing",
                    "B": "John McCarthy",
                    "C": "Marvin Minsky",
                    "D": "Ada Lovelace"
                },
                "answer": "A"
            },
            {
                "question": "Manakah yang merupakan sub-bidang Kecerdasan Buatan yang berfokus pada pengolahan bahasa manusia?",
                "options": {
                    "A": "Computer Vision",
                    "B": "Natural Language Processing (NLP)",
                    "C": "Robotics",
                    "D": "Expert Systems"
                },
                "answer": "B"
            }
        ];
        document.getElementById('questions_json').value = JSON.stringify(template, null, 2);
    }
</script>
@endsection
