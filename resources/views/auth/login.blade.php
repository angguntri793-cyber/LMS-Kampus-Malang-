<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Login') }} - LMS Kampus Malang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center relative overflow-hidden px-4">
    <!-- Background Accents -->
    <div class="absolute w-96 h-96 bg-indigo-600/30 rounded-full blur-[100px] -top-10 -left-10"></div>
    <div class="absolute w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[120px] -bottom-20 -right-20"></div>

    <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/60 rounded-3xl shadow-2xl p-10 w-full max-w-md relative z-10">
        <!-- Logo and Title -->
        <div class="text-center mb-8">
            <div class="inline-flex bg-indigo-600 text-white p-4 rounded-2xl shadow-xl shadow-indigo-600/30 mb-4 ring-4 ring-indigo-500/10">
                <i class="fas fa-graduation-cap text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-wide">LMS Kampus Malang</h1>
            <p class="text-slate-400 text-sm mt-1">Learning Management System</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-300 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-3">
                <i class="fas fa-circle-exclamation text-rose-400 text-lg shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Nomor Induk (NIM / NID)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <input name="nomor_induk" value="{{ old('nomor_induk') }}" required
                        class="w-full bg-slate-900/60 border border-slate-700/80 rounded-xl py-3 pl-10 pr-4 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition-all text-sm" 
                        placeholder="NIM / NID">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" required
                        class="w-full bg-slate-900/60 border border-slate-700/80 rounded-xl py-3 pl-10 pr-4 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition-all text-sm"
                        placeholder="••••••••">
                </div>
            </div>

            <button class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white py-3.5 rounded-xl font-semibold shadow-lg shadow-indigo-600/25 transition-all text-sm mt-2 flex items-center justify-center gap-2">
                <span>{{ __('Login') }}</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Credentials Info panel -->
        <div class="mt-8 pt-6 border-t border-slate-700/50">
            <div class="bg-slate-900/40 border border-slate-700/40 rounded-2xl p-4">
                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i class="fas fa-circle-info text-indigo-400"></i>
                    <span>Akun Demo Login</span>
                </div>
                <div class="space-y-1.5 text-xs text-slate-400">
                    <div class="flex justify-between">
                        <span>Dosen: <b class="text-white">D001</b></span>
                        <span class="text-slate-500">Pass: password</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Mahasiswa: <b class="text-white">2512345678</b></span>
                        <span class="text-slate-500">Pass: password</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Admin: <b class="text-white">admin</b></span>
                        <span class="text-slate-500">Pass: password</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>