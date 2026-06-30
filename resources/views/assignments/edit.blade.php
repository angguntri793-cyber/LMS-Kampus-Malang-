@extends('layouts.app')
@section('title', __('Edit'))
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <h2 class="text-xl font-bold mb-4">{{ __('Edit') }} - {{ $assignment->title }}</h2>
    <form method="POST" action="{{ route('assignments.update',$assignment) }}" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="block text-sm mb-1">Judul</label><input name="title" value="{{ old('title',$assignment->title) }}" required class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm mb-1">Deskripsi</label><textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description',$assignment->description) }}</textarea></div>
        <div><label class="block text-sm mb-1">{{ __('Deadline') }}</label><input type="datetime-local" name="deadline" value="{{ old('deadline',$assignment->deadline->format('Y-m-d\TH:i')) }}" required class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm mb-1">Tipe File</label><input name="allowed_types" value="{{ old('allowed_types',$assignment->allowed_types) }}" required class="w-full border rounded px-3 py-2"></div>
        <div><label class="block text-sm mb-1">Maks MB</label><input type="number" name="max_size_mb" value="{{ old('max_size_mb',$assignment->max_size_mb) }}" required class="w-full border rounded px-3 py-2"></div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('Save') }}</button>
    </form>
</div>
@endsection