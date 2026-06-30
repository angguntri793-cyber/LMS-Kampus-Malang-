@extends('layouts.app')
@section('title', __('Create Assignment'))
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <h2 class="text-xl font-bold mb-4">{{ __('Create Assignment') }} - {{ $course->name }}</h2>
    <form method="POST" action="{{ route('assignments.store',$course) }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">Judul Tugas</label>
            <input name="title" value="{{ old('title') }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Deskripsi</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">{{ __('Deadline') }}</label>
            <input type="datetime-local" name="deadline" value="{{ old('deadline') }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Tipe File Diizinkan (pisahkan koma)</label>
            <input name="allowed_types" value="{{ old('allowed_types','pdf,zip,jpg,jpeg,png,txt,doc,docx') }}" required class="w-full border rounded px-3 py-2">
            <p class="text-xs text-gray-400 mt-1">Contoh: pdf,zip,jpg,txt</p>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Maks Ukuran (MB)</label>
            <input type="number" name="max_size_mb" value="{{ old('max_size_mb',20) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('Save') }}</button>
    </form>
</div>
@endsection