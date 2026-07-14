@extends('layouts.app')

@section('title', __('Log Aktivitas Sistem'))

@section('content')
<!-- Header Banner Card -->
<div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute w-72 h-72 bg-indigo-500/20 rounded-full blur-[80px] -top-10 -right-10"></div>
    <div class="relative z-10">
        <span class="bg-indigo-500/30 text-indigo-200 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider">LMS Kampus Malang</span>
        <h2 class="text-3xl font-extrabold mt-3 tracking-tight">Log Aktivitas Sistem</h2>
        <p class="text-slate-300 text-sm mt-1.5 font-medium">Pantau aktivitas seluruh pengguna sistem perkuliahan</p>
    </div>
</div>

<div class="space-y-6">
    <!-- Search and filters -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex-1 min-w-[240px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-3.5 text-slate-400"></i>
                    <input name="search" value="{{ request('search') }}" placeholder="Cari aktivitas, nama user, NIM/NIP..." class="w-full text-sm border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                </div>
            </div>
            <div class="flex gap-3">
                <select name="role" class="text-sm border border-slate-200 rounded-xl px-4 py-2.5 outline-none bg-white">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="dosen" {{ request('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="mahasiswa" {{ request('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                </select>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.logs.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-sm px-4 py-2.5 rounded-xl transition-colors flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 font-bold">
                        <th class="px-6 py-3.5">User</th>
                        <th class="px-6 py-3.5">Aktivitas</th>
                        <th class="px-6 py-3.5">IP Address</th>
                        <th class="px-6 py-3.5">Device Agent</th>
                        <th class="px-6 py-3.5">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 leading-tight">{{ $log->user->name }}</div>
                                <div class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $log->user->nomor_induk }} ({{ ucfirst($log->user->role) }})</div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $log->activity }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-500">
                                {{ $log->ip_address }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400 max-w-xs truncate" title="{{ $log->user_agent }}">
                                {{ $log->user_agent }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-400">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">
                                Belum ada catatan aktivitas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
