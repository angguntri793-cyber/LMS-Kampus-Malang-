@extends('layouts.app')

@section('title', __('My Courses'))

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6 shadow-sm flex items-center justify-between">
    <div>
        <h2 class="text-lg font-bold text-slate-800">{{ __('My Courses') }}</h2>
        <p class="text-sm text-slate-400 mt-0.5">{{ __('Daftar mata kuliah aktif yang Anda ikuti pada semester ini.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($courses as $c)
        <div class="bg-white border border-slate-200 rounded-2xl hover:shadow-lg transition-all duration-300 flex flex-col group overflow-hidden">
            <!-- Course Color Top Strip -->
            <div class="h-3 bg-gradient-to-r from-indigo-500 to-blue-500"></div>
            
            <div class="p-6 flex-1 flex flex-col justify-between">
                <div>
                    <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">{{ $c->kode }}</span>
                    <h3 class="text-base font-bold text-slate-800 mt-3 group-hover:text-indigo-600 transition-colors leading-snug">
                        {{ $c->nama_matkul }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                        {{ $c->deskripsi ?: __('Tidak ada deskripsi.') }}
                    </p>
                </div>

                <div class="pt-6 border-t border-slate-100 mt-6 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user-tie text-indigo-400 text-sm"></i>
                        <span class="text-xs font-semibold text-slate-500 truncate max-w-[130px]">{{ $c->dosen->name }}</span>
                    </div>
                    
                    <a href="{{ route('courses.show', $c) }}" 
                       class="inline-flex items-center justify-center bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white text-xs font-bold px-3.5 py-2 rounded-xl transition-all duration-200 gap-1">
                        <span>{{ __('Buka') }}</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-16 bg-white border border-slate-200 rounded-2xl">
            <div class="bg-slate-50 text-slate-400 p-4 rounded-full inline-flex mb-4">
                <i class="fas fa-book-open text-3xl"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-base">{{ __('Belum Ada Mata Kuliah') }}</h3>
            <p class="text-slate-400 text-xs mt-1">{{ __('Anda belum terdaftar di mata kuliah manapun.') }}</p>
        </div>
    @endforelse
</div>
@endsection