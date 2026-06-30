@extends('layouts.app')
@section('title', __('Edit Profile'))
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h2 class="text-xl font-bold mb-4">{{ __('Edit Profile') }}</h2>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        <div class="flex items-center gap-4">
            <img src="{{ $user->avatar_url }}" class="w-20 h-20 rounded-full object-cover">
            <input type="file" name="avatar" class="text-sm">
        </div>
        <div><label class="block text-sm mb-1">Nama</label><input name="name" value="{{ old('name',$user->name) }}" class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm mb-1">Email</label><input name="email" value="{{ old('email',$user->email) }}" class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm mb-1">Nomor Induk</label><input value="{{ $user->nomor_induk }}" disabled class="w-full border rounded px-3 py-2 bg-gray-100"></div>
        <hr>
        <div><label class="block text-sm mb-1">Password Baru (kosongkan jika tidak diubah)</label><input type="password" name="password" class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm mb-1">Konfirmasi Password</label><input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2"></div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('Save') }}</button>
    </form>
</div>
@endsection