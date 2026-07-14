@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<!-- Header Banner Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">LMS Kampus Malang</span>
        <h2 class="text-3xl font-extrabold mt-3 tracking-tight">
            {{ __('Welcome back') }}, {{ $user->name }}!
        </h2>
        <p class="text-slate-300 text-sm mt-1.5 font-medium flex items-center gap-2">
            <span class="bg-indigo-600/50 px-2.5 py-0.5 rounded-md text-xs font-semibold">{{ strtoupper($user->role) }}</span>
            <span>•</span>
            <span>{{ $user->nomor_induk }}</span>
            @if($user->jurusan)
                <span>•</span>
                <span>{{ $user->jurusan }}</span>
            @endif
        </p>
    </div>
</div>

<!-- Dynamic Layout according to User Role -->
@if($user->isAdmin())
    <!-- ============================================== -->
    <!-- ADMINISTRATOR DASHBOARD VIEW                   -->
    <!-- ============================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="bg-blue-50 text-blue-600 p-4 rounded-xl">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <div>
                <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('Students') }}</div>
                <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['total_students'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="bg-amber-50 text-amber-600 p-4 rounded-xl">
                <i class="fas fa-user-tie text-2xl"></i>
            </div>
            <div>
                <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('Lecturer') }}</div>
                <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['total_dosens'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="bg-indigo-50 text-indigo-600 p-4 rounded-xl">
                <i class="fas fa-book-open text-2xl"></i>
            </div>
            <div>
                <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('My Courses') }}</div>
                <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['total_courses'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl">
                <i class="fas fa-building-columns text-2xl"></i>
            </div>
            <div>
                <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Fakultas / Prodi</div>
                <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['total_prodi'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="bg-rose-50 text-rose-600 p-4 rounded-xl">
                <i class="fas fa-users-rectangle text-2xl"></i>
            </div>
            <div>
                <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Kelas</div>
                <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['total_kelas'] }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
            <div class="bg-slate-50 text-slate-600 p-4 rounded-xl">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div>
                <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Log Aktivitas</div>
                <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['total_activities'] }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Area: Activity Logs & Announcements -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Recent Activity Logs -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fas fa-list-ul text-indigo-500"></i>
                        <span>Aktivitas Sistem Terbaru</span>
                    </h3>
                    <a href="{{ route('admin.logs.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition-colors">
                        {{ __('Lihat Semua') }} <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentActivities as $act)
                        <div class="py-3.5 flex justify-between items-start gap-4">
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-800 flex items-center gap-1.5">
                                    <span>{{ $act->user->name }}</span>
                                    <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-semibold">{{ strtoupper($act->user->role) }}</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">{{ $act->activity }}</p>
                                <span class="text-[10px] text-slate-400 font-medium block mt-1"><i class="fas fa-desktop"></i> IP: {{ $act->ip_address }}</span>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400 shrink-0">{{ $act->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 py-6 text-center text-sm font-medium">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>

            <!-- Global Announcements -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fas fa-bullhorn text-indigo-500"></i>
                        <span>Pengumuman Kampus</span>
                    </h3>
                    <a href="{{ route('admin.pengumuman.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition-colors">
                        Kelola <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($announcements as $ann)
                        <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-slate-800 text-sm">{{ $ann->judul }}</h4>
                                <span class="text-[10px] text-indigo-600 font-semibold bg-indigo-50 px-2 py-0.5 rounded uppercase">Target: {{ $ann->target_role }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $ann->konten }}</p>
                            <div class="text-[10px] text-slate-400 mt-3 font-semibold">
                                Diposting oleh {{ $ann->user->name }} • {{ $ann->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 py-6 text-center text-sm font-medium">Belum ada pengumuman.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Area (Notifications panel) -->
        <div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 h-full flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fas fa-bell text-indigo-500"></i>
                        <span>{{ __('Notifikasi') }}</span>
                    </h3>
                    <a href="{{ route('notifications.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition-colors">
                        {{ __('Semua') }}
                    </a>
                </div>

                <div class="space-y-4 flex-1 overflow-y-auto max-h-[480px]">
                    @forelse($notifications as $notification)
                        <a href="{{ route('notifications.show', $notification) }}" 
                           class="block p-4 border rounded-xl transition-all duration-200 {{ $notification->is_read ? 'bg-slate-50/50 border-slate-100 text-slate-600' : 'bg-indigo-50/30 border-indigo-100 text-slate-800 hover:bg-indigo-50/50' }}">
                            <div class="flex justify-between items-start gap-2">
                                <div class="font-bold text-xs truncate max-w-[85%]">{{ $notification->title }}</div>
                                @if(!$notification->is_read)
                                    <span class="w-2 h-2 bg-indigo-600 rounded-full shrink-0 mt-1"></span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                                <i class="far fa-clock"></i>
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                            </p>
                        </a>
                    @empty
                        <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-200 h-full flex flex-col justify-center items-center">
                            <i class="far fa-bell-slash text-slate-300 text-2xl mb-2"></i>
                            <p class="text-slate-400 text-xs">{{ __('Tidak ada notifikasi.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    <!-- ============================================== -->
    <!-- DOSEN & MAHASISWA DASHBOARD VIEW               -->
    <!-- ============================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @if($user->isDosen())
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="bg-blue-50 text-blue-600 p-4 rounded-xl">
                    <i class="fas fa-book-bookmark text-2xl"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('My Courses') }}</div>
                    <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['courses_count'] }}</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('Students') }}</div>
                    <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['students_count'] }}</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="bg-amber-50 text-amber-600 p-4 rounded-xl">
                    <i class="fas fa-file-signature text-2xl"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('Assignments') }}</div>
                    <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['assignments_count'] }}</div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="bg-blue-50 text-blue-600 p-4 rounded-xl">
                    <i class="fas fa-book text-2xl"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('My Courses') }}</div>
                    <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['courses_count'] }}</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="bg-rose-50 text-rose-600 p-4 rounded-xl">
                    <i class="fas fa-clock-rotate-left text-2xl"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">{{ __('Upcoming Activities') }}</div>
                    <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['assignments_count'] }}</div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center gap-5">
                <div class="bg-emerald-50 text-emerald-600 p-4 rounded-xl">
                    <i class="fas fa-circle-check text-2xl"></i>
                </div>
                <div>
                    <div class="text-slate-400 text-xs font-bold uppercase tracking-wider">Rata-rata Nilai</div>
                    <div class="text-2xl font-bold text-slate-800 mt-0.5">{{ $stats['average_grade'] }}</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Area (Courses & Assignments) -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Courses Grid -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fas fa-book-open text-indigo-500"></i>
                        <span>{{ __('My Courses') }}</span>
                    </h3>
                    <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition-colors">
                        {{ __('Lihat Semua') }} <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($courses as $course)
                        <a href="{{ route('courses.show', $course) }}" 
                           class="block border border-slate-100 hover:border-indigo-100 bg-slate-50/50 hover:bg-indigo-50/20 rounded-xl p-5 transition-all duration-200 hover:shadow-sm">
                            <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider">{{ $course->kode }}</div>
                            <h4 class="font-bold text-slate-800 mt-1.5 leading-snug group-hover:text-indigo-600 truncate">{{ $course->nama_matkul }}</h4>
                            
                            @if($user->isDosen())
                                <div class="flex gap-4 mt-4 text-xs font-semibold text-slate-400">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-file-signature text-amber-500/80"></i> {{ $course->assignments_count }} {{ __('Tugas') }}</span>
                                    <span class="flex items-center gap-1.5"><i class="fas fa-user-graduate text-emerald-500/80"></i> {{ $course->students_count }} {{ __('Mahasiswa') }}</span>
                                </div>
                            @else
                                <div class="text-xs text-slate-400 mt-4 flex items-center gap-1.5 font-medium">
                                    <i class="fas fa-user-tie text-indigo-400"></i>
                                    <span>{{ $course->dosen->name }}</span>
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="sm:col-span-2 text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-slate-400 text-sm">{{ __('Belum ada mata kuliah.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Assignments -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2 mb-6">
                    <i class="fas fa-clock text-indigo-500"></i>
                    <span>{{ __('Tugas Mendatang') }}</span>
                </h3>

                <div class="divide-y divide-slate-100">
                    @forelse($upcoming as $assignment)
                        <div class="flex items-center justify-between py-4 first:pt-0 last:pb-0 gap-4">
                            <div class="min-w-0">
                                <a href="{{ route('assignments.show', $assignment) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition-colors text-sm truncate block">
                                    {{ $assignment->judul }}
                                </a>
                                <div class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                                    <i class="fas fa-book-bookmark text-indigo-400/70"></i>
                                    <span>{{ $assignment->course->nama_matkul }}</span>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="inline-flex bg-rose-50 text-rose-700 text-xs font-semibold px-2.5 py-1 rounded-lg border border-rose-100">
                                    {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-slate-400 text-sm">{{ __('Tidak ada tugas mendatang.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Area (Notifications panel) -->
        <div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 h-full flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <i class="fas fa-bell text-indigo-500"></i>
                        <span>{{ __('Notifikasi') }}</span>
                    </h3>
                    <a href="{{ route('notifications.index') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm transition-colors">
                        {{ __('Semua') }}
                    </a>
                </div>

                <div class="space-y-4 flex-1 overflow-y-auto max-h-[480px]">
                    @forelse($notifications as $notification)
                        <a href="{{ route('notifications.show', $notification) }}" 
                           class="block p-4 border rounded-xl transition-all duration-200 {{ $notification->is_read ? 'bg-slate-50/50 border-slate-100 text-slate-600' : 'bg-indigo-50/30 border-indigo-100 text-slate-800 hover:bg-indigo-50/50' }}">
                            <div class="flex justify-between items-start gap-2">
                                <div class="font-bold text-xs truncate max-w-[85%]">{{ $notification->title }}</div>
                                @if(!$notification->is_read)
                                    <span class="w-2 h-2 bg-indigo-600 rounded-full shrink-0 mt-1"></span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                                <i class="far fa-clock"></i>
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                            </p>
                        </a>
                    @empty
                        <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-200 h-full flex flex-col justify-center items-center">
                            <i class="far fa-bell-slash text-slate-300 text-2xl mb-2"></i>
                            <p class="text-slate-400 text-xs">{{ __('Tidak ada notifikasi.') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endif
@endsection