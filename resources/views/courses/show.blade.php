@extends('layouts.app')

@section('title', $course->nama_matkul)

@section('content')
<!-- Back navigation -->
<div class="mb-5 flex items-center justify-between">
    <a href="{{ route('courses.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-800 text-sm font-semibold transition-colors gap-1.5">
        <i class="fas fa-arrow-left text-xs"></i>
        <span>{{ __('Kembali ke Daftar Mata Kuliah') }}</span>
    </a>
</div>

<!-- Course Header Banner -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-64 h-64 bg-indigo-500/10 rounded-full blur-[70px] -bottom-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-600/60 text-indigo-100 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">{{ $course->kode }}</span>
        <h2 class="text-2xl font-extrabold mt-3 tracking-tight">{{ $course->nama_matkul }}</h2>
        <p class="text-slate-300 text-sm mt-2 leading-relaxed max-w-3xl">
            {{ $course->deskripsi ?: __('Tidak ada deskripsi.') }}
        </p>
        
        <div class="mt-6 pt-6 border-t border-slate-800 flex flex-wrap items-center gap-6 text-xs font-semibold text-slate-400">
            <span class="flex items-center gap-1.5"><i class="fas fa-user-tie text-indigo-400"></i> {{ __('Dosen') }}: {{ $course->dosen->name }}</span>
            <span class="flex items-center gap-1.5"><i class="fas fa-circle-info text-slate-500"></i> {{ $course->kode }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column (Assignments List) -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <i class="fas fa-file-signature text-indigo-500"></i>
                    <span>{{ __('Assignments') }}</span>
                </h3>
                @if(auth()->user()->isDosen() && $course->dosen_id === auth()->id())
                    <a href="{{ route('assignments.create', $course) }}" 
                       class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all duration-200 shadow-sm shadow-indigo-600/20 gap-1.5">
                        <i class="fas fa-plus"></i>
                        <span>{{ __('Create Assignment') }}</span>
                    </a>
                @endif
            </div>

            <div class="space-y-4">
                @forelse($course->assignments as $a)
                    <div class="border border-slate-100 hover:border-indigo-100 bg-slate-50/50 hover:bg-indigo-50/10 rounded-xl p-5 transition-all duration-200 flex justify-between items-center gap-4">
                        <div class="min-w-0">
                            <a href="{{ route('assignments.show', $a) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition-colors text-sm truncate block">
                                {{ $a->judul }}
                            </a>
                            <div class="text-xs text-slate-400 mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 font-medium">
                                <span class="flex items-center gap-1"><i class="far fa-clock text-rose-400"></i> {{ __('Tenggat') }}: {{ $a->deadline->format('d M Y, H:i') }}</span>
                                <span class="flex items-center gap-1"><i class="far fa-file-lines text-slate-400"></i> {{ $a->allowed_types }}</span>
                            </div>
                        </div>
                        
                        @if(auth()->user()->isDosen() && $course->dosen_id === auth()->id())
                            <div class="flex gap-1.5 shrink-0">
                                <a href="{{ route('assignments.edit', $a) }}" class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="{{ __('Edit') }}">
                                    <i class="fas fa-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('assignments.destroy', $a) }}" onsubmit="return confirm('{{ __('Hapus tugas ini?') }}')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <p class="text-slate-400 text-sm font-medium">{{ __('Belum ada tugas.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column (Course Notes) -->
    <div class="space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2 mb-6">
                <i class="fas fa-file-lines text-indigo-500"></i>
                <span>{{ __('Course Notes') }}</span>
            </h3>

            <!-- Lecturer CRUD Notes Form -->
            @if(auth()->user()->isDosen() && $course->dosen_id === auth()->id())
                <div class="border border-indigo-100 rounded-xl p-4 bg-indigo-50/20 mb-6" x-data="{open:false}">
                    <button class="w-full flex items-center justify-between font-bold text-xs text-indigo-700 tracking-wide uppercase" @click="open = !open">
                        <span><i class="fas fa-plus-circle mr-1"></i> {{ __('Add Note') }}</span>
                        <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    
                    <form method="POST" action="{{ route('notes.store', $course) }}" enctype="multipart/form-data" class="mt-4 space-y-3" x-show="open">
                        @csrf
                        <div>
                            <input name="judul" required placeholder="{{ __('Title') }}" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2.5 focus:ring-1 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none">
                        </div>
                        <div>
                            <textarea name="isi" required placeholder="{{ __('Content') }}..." rows="3" class="w-full text-xs border border-slate-200 rounded-lg px-3 py-2.5 focus:ring-1 focus:ring-indigo-500/30 focus:border-indigo-500 outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Attachment</label>
                            <input type="file" name="file" class="block w-full text-xs text-slate-400 file:mr-2 file:py-1 file:px-3.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                        </div>
                        <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 rounded-lg transition-colors shadow-sm">
                            {{ __('Save') }}
                        </button>
                    </form>
                </div>
            @endif

            <!-- Notes List -->
            <div class="space-y-4">
                @forelse($course->notes as $note)
                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/30 relative group">
                        <h4 class="font-bold text-slate-800 text-sm leading-tight pr-6">{{ $note->judul }}</h4>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed whitespace-pre-line">{{ $note->isi }}</p>
                        
                        <!-- Attachment Link -->
                        @if($note->file_path)
                            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[10px] text-slate-400 font-medium truncate max-w-[140px]"><i class="fas fa-paperclip mr-1"></i> {{ $note->file_name }}</span>
                                <a href="{{ route('notes.download', $note) }}" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors gap-1">
                                    <i class="fas fa-download text-[10px]"></i>
                                    <span>{{ __('Download') }}</span>
                                </a>
                            </div>
                        @endif

                        <!-- Delete Button for Lecturer -->
                        @if(auth()->user()->isDosen() && $course->dosen_id === auth()->id())
                            <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form method="POST" action="{{ route('notes.destroy', $note) }}" onsubmit="return confirm('{{ __('Hapus catatan ini?') }}')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:text-rose-700" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <p class="text-slate-400 text-xs font-medium">{{ __('No lecture notes available.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Simple Alpine.js for dropdown transitions -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection