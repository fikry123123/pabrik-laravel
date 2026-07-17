<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login – PabrikPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-100 h-screen flex items-center justify-center font-sans">

    <div class="bg-white p-10 rounded-3xl shadow-xl w-96 border border-slate-200">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="factory" size="32"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tighter">
                PABRIK<span class="text-blue-500">PRO</span>
            </h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Silakan login untuk melanjutkan</p>
        </div>

        @if($errors->any())
            <div class="bg-rose-100 text-rose-600 p-3 rounded-xl mb-4 text-sm font-bold text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Username</label>
                <input type="text" name="username" value="{{ old('username') }}"
                       class="w-full p-4 border bg-slate-50 rounded-xl mt-1 font-bold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all"
                       required autofocus>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Password</label>
                <input type="password" name="password"
                       class="w-full p-4 border bg-slate-50 rounded-xl mt-1 font-bold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all"
                       required>
            </div>
            <button type="submit"
                    class="w-full bg-blue-600 text-white font-black py-4 rounded-xl shadow-lg hover:bg-blue-700 transition-all">
                LOGIN
            </button>
        </form>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
