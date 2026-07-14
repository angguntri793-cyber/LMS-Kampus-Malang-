@extends('layouts.app')

@section('title', __('Pesan Pribadi'))

@section('content')
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex h-[75vh]">
    <!-- Contacts Sidebar -->
    <div class="w-80 border-r border-slate-200 flex flex-col bg-slate-50/50">
        <!-- Search bar -->
        <div class="p-4 border-b border-slate-200 bg-white">
            <form method="GET" action="{{ route('chat.index') }}" class="relative m-0">
                <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                <input name="search" value="{{ request('search') }}" placeholder="Cari kontak..." 
                    class="w-full bg-slate-100 text-xs border-0 rounded-xl pl-9 pr-4 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:bg-white outline-none transition-all">
            </form>
        </div>

        <!-- Contacts list -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
            @forelse($contacts as $contact)
                <a href="{{ route('chat.index', $contact) }}" 
                   class="flex items-center gap-3 p-4 hover:bg-white transition-all {{ ($active && $active->id === $contact->id) ? 'bg-white shadow-sm ring-1 ring-slate-100 font-semibold' : '' }}">
                    <div class="relative shrink-0">
                        <img src="{{ $contact->avatar_url }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-slate-100">
                        @if($contact->isOnline())
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full border-2 border-white"></span>
                        @else
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-slate-300 rounded-full border-2 border-white"></span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-800 truncate block">{{ $contact->name }}</span>
                            @if($contact->unread_count > 0)
                                <span class="bg-indigo-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shrink-0">
                                    {{ $contact->unread_count }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-400 block mt-0.5">{{ ucfirst($contact->role) }}</span>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs font-medium">
                    Tidak ada kontak ditemukan.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Active Chat Panel -->
    <div class="flex-1 flex flex-col bg-slate-50/20">
        @if($active)
            <!-- Header -->
            <div class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <img src="{{ $active->avatar_url }}" class="w-10 h-10 rounded-xl object-cover">
                        @if($active->isOnline())
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                        @endif
                    </div>
                    <div>
                        <div class="font-bold text-sm text-slate-800 leading-tight">{{ $active->name }}</div>
                        <span class="text-[10px] text-slate-400 block mt-0.5">
                            @if($active->isOnline())
                                <span class="text-emerald-500 font-semibold">Online</span>
                            @else
                                Last seen: {{ $active->last_seen_at ? $active->last_seen_at->diffForHumans() : 'offline' }}
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Message Search Form inside Chat -->
                <form method="GET" action="{{ route('chat.index', $active) }}" class="relative m-0 hidden sm:block">
                    <i class="fas fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <input name="msg_search" value="{{ request('msg_search') }}" placeholder="Cari di obrolan..." 
                        class="bg-slate-100 text-[11px] rounded-lg pl-8 pr-4 py-2 border-0 outline-none focus:ring-1 focus:ring-indigo-500/20 focus:bg-white transition-all w-48">
                    @if(request('msg_search'))
                        <a href="{{ route('chat.index', $active) }}" class="absolute right-2 top-2 text-xs text-slate-400 hover:text-slate-650">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Messages Log -->
            <div id="chatbox" class="flex-1 p-6 overflow-y-auto space-y-4">
                @forelse($messages as $m)
                    @php $isMine = ($m->sender_id === auth()->id()); @endphp
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} group/msg">
                        <div class="max-w-[70%] flex items-end gap-2">
                            @if(!$isMine)
                                <img src="{{ $active->avatar_url }}" class="w-6 h-6 rounded-lg object-cover mb-1">
                            @endif
                            <div class="relative">
                                <div class="px-4 py-2.5 rounded-2xl text-xs leading-relaxed shadow-sm
                                    {{ $isMine 
                                        ? 'bg-indigo-600 text-white rounded-br-none' 
                                        : 'bg-slate-100 text-slate-800 rounded-bl-none border border-slate-200' 
                                    }}">
                                    {{ $m->body }}
                                    <div class="text-[9px] mt-1.5 text-right font-medium opacity-60 flex items-center justify-end gap-1">
                                        <span>{{ $m->created_at->format('H:i') }}</span>
                                        @if($isMine)
                                            @if($m->is_read)
                                                <i class="fas fa-check-double text-sky-300"></i>
                                            @else
                                                <i class="fas fa-check"></i>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Delete message button -->
                                <button onclick="deleteMessage({{ $m->id }})" 
                                    class="absolute -top-2 {{ $isMine ? '-left-6' : '-right-6' }} opacity-0 group-hover/msg:opacity-100 hover:text-rose-600 text-slate-300 p-1 text-[10px] transition-all"
                                    title="Hapus untuk saya">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center text-slate-400 text-xs">
                        @if(request('msg_search'))
                            Pesan tidak ditemukan.
                        @else
                            Kirim pesan pertama untuk memulai obrolan.
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Send Message Form -->
            <form id="chat-form" method="POST" action="{{ route('chat.send', $active) }}" class="p-4 bg-white border-t border-slate-200 flex gap-2 shadow-sm sticky bottom-0">
                @csrf
                <input id="chat-input" name="body" required autocomplete="off" placeholder="Ketik pesan..." 
                    class="flex-1 border border-slate-200 rounded-xl px-4 py-3 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all bg-slate-50/50">
                <button class="bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-850 text-white px-5 rounded-xl font-semibold shadow-md transition-all flex items-center justify-center">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </form>
        @else
            <!-- Placeholder -->
            <div class="flex-1 flex flex-col items-center justify-center text-slate-400 p-8">
                <div class="bg-indigo-50 text-indigo-500 p-5 rounded-3xl shadow-sm mb-4 ring-8 ring-indigo-500/5">
                    <i class="far fa-comments text-3xl"></i>
                </div>
                <h4 class="font-bold text-slate-700 text-sm">Obrolan Pribadi</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-xs text-center leading-relaxed">Pilih salah satu kontak di sebelah kiri untuk melihat percakapan atau mengirim pesan baru.</p>
            </div>
        @endif
    </div>
</div>

@if($active)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const box = document.getElementById('chatbox');
        if (box) {
            box.scrollTop = box.scrollHeight;
        }

        // AJAX submit message
        const form = document.getElementById('chat-form');
        const input = document.getElementById('chat-input');
        
        if (form) {
            form.addEventListener('submit', async function (e) {
                e.preventDefault();
                const text = input.value.trim();
                if (!text) return;

                input.value = '';
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ body: text })
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        appendMessage(data.message);
                    }
                } catch (err) {
                    console.error("Gagal mengirim pesan:", err);
                }
            });
        }

        function appendMessage(m) {
            const html = `
                <div class="flex justify-end group/msg">
                    <div class="max-w-[70%] flex items-end gap-2">
                        <div class="relative">
                            <div class="px-4 py-2.5 rounded-2xl text-xs leading-relaxed shadow-sm bg-indigo-600 text-white rounded-br-none">
                                ${escapeHtml(m.body)}
                                <div class="text-[9px] mt-1.5 text-right font-medium opacity-60 flex items-center justify-end gap-1">
                                    <span>${m.time}</span>
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <button onclick="deleteMessage(${m.id})" 
                                class="absolute -top-2 -left-6 opacity-0 group-hover/msg:opacity-100 hover:text-rose-600 text-slate-300 p-1 text-[10px] transition-all"
                                title="Hapus untuk saya">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            box.insertAdjacentHTML('beforeend', html);
            box.scrollTop = box.scrollHeight;
        }

        function escapeHtml(string) {
            return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        // Fetch updates via polling (every 4 seconds)
        setInterval(async () => {
            if (document.activeElement === input) return; // Skip polling if typing to prevent scroll jank
            try {
                const res = await fetch("{{ route('chat.fetch', $active) }}");
                if (res.ok) {
                    const data = await res.json();
                    const me = {{ auth()->id() }};
                    
                    let html = '';
                    data.forEach(m => {
                        const isMine = m.sender_id === me;
                        html += `
                            <div class="flex ${isMine ? 'justify-end' : 'justify-start'} group/msg">
                                <div class="max-w-[70%] flex items-end gap-2">
                                    ${!isMine ? `<img src="${"{{ $active->avatar_url }}"}" class="w-6 h-6 rounded-lg object-cover mb-1">` : ''}
                                    <div class="relative">
                                        <div class="px-4 py-2.5 rounded-2xl text-xs leading-relaxed shadow-sm
                                            ${isMine 
                                                ? 'bg-indigo-600 text-white rounded-br-none' 
                                                : 'bg-slate-100 text-slate-800 rounded-bl-none border border-slate-200' 
                                            }">
                                            ${escapeHtml(m.body)}
                                            <div class="text-[9px] mt-1.5 text-right font-medium opacity-60 flex items-center justify-end gap-1">
                                                <span>${m.time}</span>
                                                ${isMine ? '<i class="fas fa-check-double text-sky-300"></i>' : ''}
                                            </div>
                                        </div>
                                        <button onclick="deleteMessage(${m.id})" 
                                            class="absolute -top-2 ${isMine ? '-left-6' : '-right-6'} opacity-0 group-hover/msg:opacity-100 hover:text-rose-600 text-slate-300 p-1 text-[10px] transition-all"
                                            title="Hapus untuk saya">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                    });
                    
                    const isAtBottom = box.scrollHeight - box.clientHeight - box.scrollTop < 100;
                    box.innerHTML = html;
                    if (isAtBottom) {
                        box.scrollTop = box.scrollHeight;
                    }
                }
            } catch (err) {
                console.error("Gagal melakukan polling pesan:", err);
            }
        }, 4000);
    });

    async function deleteMessage(messageId) {
        if (!confirm('Hapus pesan ini dari riwayat Anda?')) return;
        
        try {
            const response = await fetch(`/messages/${messageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            if (response.ok) {
                window.location.reload();
            }
        } catch (err) {
            console.error("Gagal menghapus pesan:", err);
        }
    }
</script>
@endif
@endsection
