@extends('layouts.app')
@section('title', __('Online Users'))
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold mb-4"><i class="fas fa-circle text-green-500 text-sm"></i> {{ __('Online Users') }}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
        @forelse($users as $u)
            <a href="{{ route('chat.show',$u) }}" class="flex items-center gap-3 border rounded-lg p-3 hover:shadow">
                <div class="relative">
                    <img src="{{ $u->avatar_url }}" class="w-10 h-10 rounded-full">
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                </div>
                <div>
                    <div class="font-medium text-sm">{{ $u->name }}</div>
                    <div class="text-xs text-gray-500">{{ ucfirst($u->role) }}</div>
                </div>
            </a>
        @empty
            <p class="text-gray-500 text-sm">Tidak ada pengguna online.</p>
        @endforelse
    </div>
</div>
@endsection