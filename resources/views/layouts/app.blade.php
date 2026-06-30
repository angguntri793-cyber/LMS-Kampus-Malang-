<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', __('Dashboard'))</title>
    
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Framework & Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom styling with Outfit font -->
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800 shrink-0">
        <div class="p-6 border-b border-slate-800 flex items-center gap-3">
            <div class="bg-indigo-600 text-white p-2.5 rounded-xl shadow-lg shadow-indigo-600/30">
                <i class="fas fa-graduation-cap text-xl"></i>
            </div>
            <div>
                <div class="font-bold text-white text-lg tracking-wide leading-none">LMS Kampus</div>
                <div class="text-xs text-slate-500 font-medium tracking-wider mt-1">MALANG</div>
            </div>
        </div>
        
        <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
            @php
                $route = Route::currentRouteName();
            @endphp

            @if(auth()->user()->isAdmin())
                <div class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('User Management') }}</div>
                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_contains($route, 'admin.users') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-users-cog text-lg"></i>
                    <span>{{ __('User Management') }}</span>
                </a>
            @else
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $route == 'dashboard' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-chart-pie text-lg"></i>
                    <span>{{ __('Dashboard') }}</span>
                </a>
                
                <a href="{{ route('courses.index') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ str_contains($route, 'courses') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-book-open text-lg"></i>
                    <span>{{ __('My Courses') }}</span>
                </a>
            @endif

            <a href="{{ route('online.index') }}" 
               class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ str_contains($route, 'online') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-circle-dot text-lg text-emerald-400"></i>
                    <span>{{ __('Online Users') }}</span>
                </div>
                <span id="online-counter" class="bg-slate-800 text-slate-300 text-xs px-2 py-0.5 rounded-full font-medium border border-slate-700">...</span>
            </a>
            
            <a href="{{ route('chat.index') }}" 
               class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ str_contains($route, 'chat') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-message text-lg"></i>
                    <span>{{ __('Messages') }}</span>
                </div>
            </a>
            
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ $route == 'profile.edit' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 font-medium' : 'hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-user-gear text-lg"></i>
                <span>{{ __('Edit Profile') }}</span>
            </a>
        </nav>

        <!-- Current User Profile Card in Sidebar Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center gap-3">
                <div class="relative shrink-0">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/'.auth()->user()->avatar) }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-indigo-500/20">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff" class="w-10 h-10 rounded-xl object-cover ring-2 ring-indigo-500/20">
                    @endif
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full border-2 border-slate-900"></span>
                </div>
                <div class="overflow-hidden">
                    <div class="text-white text-sm font-semibold truncate leading-tight">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-500 truncate mt-0.5">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Pane -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 flex items-center justify-between px-8 py-4 sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold text-slate-800">@yield('title', __('Dashboard'))</h1>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- Translation Switcher -->
                <div class="bg-slate-100 p-0.5 rounded-lg flex items-center gap-0.5">
                    <a href="{{ route('locale.set', 'id') }}" 
                       class="px-3 py-1 text-xs font-semibold rounded-md transition-all duration-200 {{ app()->getLocale() == 'id' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                        ID
                    </a>
                    <a href="{{ route('locale.set', 'en') }}" 
                       class="px-3 py-1 text-xs font-semibold rounded-md transition-all duration-200 {{ app()->getLocale() == 'en' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                        EN
                    </a>
                </div>

                <!-- Notifications Dropdown -->
                <div class="relative">
                    @php
                        $unread = auth()->user()->appNotifications()->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('notifications.index') }}" class="relative text-slate-400 hover:text-slate-600 transition-colors p-1">
                        <i class="far fa-bell text-xl"></i>
                        @if($unread > 0)
                            <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white rounded-full text-[10px] font-bold px-1.5 py-0.5 leading-none ring-2 ring-white animate-pulse">
                                {{ $unread }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Divider -->
                <span class="w-px h-6 bg-slate-200"></span>

                <!-- Logout Icon Link -->
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="text-slate-400 hover:text-rose-600 transition-colors p-1" title="{{ __('Logout') }}">
                        <i class="fas fa-arrow-right-from-bracket text-xl"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Workspace -->
        <main class="p-8 flex-1 overflow-y-auto">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-xl flex items-center gap-3 shadow-sm">
                    <i class="fas fa-circle-check text-lg text-emerald-500"></i>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-xl shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-circle-exclamation text-lg text-rose-500"></i>
                        <span class="text-sm font-semibold">Terdapat beberapa kesalahan:</span>
                    </div>
                    <ul class="list-disc ml-8 text-sm space-y-0.5 font-medium">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
</div>

<!-- Global AJAX Script to fetch online user counter dynamically -->
<script>
    async function updateOnlineCounter() {
        try {
            const res = await fetch("{{ route('online.api') }}");
            if (res.ok) {
                const users = await res.json();
                const counter = document.getElementById('online-counter');
                if (counter) {
                    counter.innerText = users.length;
                }
            }
        } catch (e) {
            console.error("Gagal memperbarui jumlah pengguna online:", e);
        }
    }
    
    // Poll online count every 15 seconds
    updateOnlineCounter();
    setInterval(updateOnlineCounter, 15000);
</script>
@yield('scripts')
</body>
</html>