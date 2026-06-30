@extends('layouts.app')
@section('title', __('Notifications'))
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <h2 class="text-xl font-bold mb-4">{{ $notification->title }}</h2>

    @if($notification->type === 'signin')
        <div class="text-gray-700 space-y-1">
            <p>Your account: <b>{{ $content['account'] }}</b></p>
            <ul class="list-disc ml-6 text-sm text-gray-600">
                <li>{{ $content['time'] }}</li>
                <li>Device: {{ $content['device'] }}</li>
                <li>IP: {{ $content['ip'] }}</li>
            </ul>
        </div>
    @elseif($notification->type === 'activity_due')
        <div class="text-gray-700 space-y-2">
            <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
            <p>Hi {{ $content['nim'] }},</p>
            <p>You have upcoming activities due:</p>
            <p>Activity event <b>{{ $content['activity'] }}</b> is due</p>
            <p>When: {{ $content['when'] }}</p>
            <p>Course: {{ $content['course'] }}</p>
            <a href="{{ route('assignments.show', $content['assignment_id']) }}" class="inline-block mt-2 text-blue-600 underline">Go to activity</a>
        </div>
    @endif
</div>
@endsection