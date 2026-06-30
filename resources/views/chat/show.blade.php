@extends('layouts.app')
@section('title', 'Chat - '.$user->name)
@section('content')
<div class="flex gap-4 h-[70vh]">
    <!-- contacts -->
    <div class="w-64 bg-white rounded-lg shadow overflow-y-auto">
        @foreach($users as $u)
            <a href="{{ route('chat.show',$u) }}" class="flex items-center gap-2 p-3 border-b hover:bg-gray-50 {{ $u->id==$user->id?'bg-blue-50':'' }}">
                <img src="{{ $u->avatar_url }}" class="w-8 h-8 rounded-full">
                <div class="text-sm">{{ $u->name }} @if($u->isOnline())<span class="text-green-500">●</span>@endif</div>
            </a>
        @endforeach
    </div>
    <!-- chat -->
    <div class="flex-1 bg-white rounded-lg shadow flex flex-col">
        <div class="p-3 border-b font-semibold flex items-center gap-2">
            <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full"> {{ $user->name }}
        </div>
        <div id="chatbox" class="flex-1 p-4 overflow-y-auto space-y-2">
            @foreach($messages as $m)
                <div class="flex {{ $m->sender_id==auth()->id()?'justify-end':'justify-start' }}">
                    <div class="max-w-xs px-3 py-2 rounded-lg text-sm {{ $m->sender_id==auth()->id()?'bg-blue-600 text-white':'bg-gray-200' }}">
                        {{ $m->body }}
                        <div class="text-[10px] opacity-70 mt-1">{{ $m->created_at->format('H:i') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <form method="POST" action="{{ route('chat.send',$user) }}" class="p-3 border-t flex gap-2">
            @csrf
            <input name="body" required class="flex-1 border rounded px-3 py-2" placeholder="Ketik pesan...">
            <button class="bg-blue-600 text-white px-4 rounded"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>
<script>
    const box = document.getElementById('chatbox');
    box.scrollTop = box.scrollHeight;
    // Polling untuk pesan baru tiap 3 detik
    setInterval(async () => {
        const res = await fetch("{{ route('chat.fetch',$user) }}");
        const data = await res.json();
        const me = {{ auth()->id() }};
        box.innerHTML = data.map(m => `
            <div class="flex ${m.sender_id==me?'justify-end':'justify-start'}">
                <div class="max-w-xs px-3 py-2 rounded-lg text-sm ${m.sender_id==me?'bg-blue-600 text-white':'bg-gray-200'}">
                    ${m.body.replace(/</g,'&lt;')}
                    <div class="text-[10px] opacity-70 mt-1">${new Date(m.created_at).toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'})}</div>
                </div>
            </div>`).join('');
        box.scrollTop = box.scrollHeight;
    }, 3000);
</script>
@endsection