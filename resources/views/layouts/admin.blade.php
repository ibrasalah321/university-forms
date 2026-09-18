<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'بوابة الإدارة الإلكترونية') - جامعة الأمير سطام</title>
    
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .bg-psau-teal { background-color: #3c7974; }
        .text-psau-teal { color: #3c7974; }
        .border-psau-teal { border-color: #3c7974; }
        .hover-psau:hover { background-color: rgba(74, 145, 136, 0.1); color: #3c7974; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col">

    <div class="navbar bg-white shadow-sm border-b border-slate-200 px-6 z-10">
        <div class=" flex-1 gap-3">
            <a href="/dashboard"><img src="{{ asset('images/logo.svg') }}" alt="شعار جامعة الأمير سطام" class="w-40 "></a>
            <span class="text-lg font-bold text-slate-800 border-r pr-3 border-slate-300">نظام النماذج الديناميكية</span>
        </div>
        <div class="flex-none gap-4">
            <div class="text-sm font-semibold text-slate-600 hidden md:block">
                مرحباً، <span class="text-psau-teal">{{ Auth::user()->name }}</span>
            </div>
            <form action="/logout" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm text-red-500 font-bold rounded-lg hover:bg-red-50">
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </div>

    <div class="flex flex-1">
        
        <aside class="w-64 bg-white border-l border-slate-200 p-4 hidden md:flex flex-col justify-between">
            <ul class="menu menu-md w-full p-0 space-y-1">
                <li class="menu-title text-slate-400 font-bold text-xs mb-2">القائمة الرئيسية</li>
                <li>
                    <a href="{{ route('dashboard') }}" class="font-semibold text-slate-700 hover-psau {{ request()->is('dashboard') ? 'bg-slate-100 text-psau-teal' : '' }}">
                        📊 لوحة التحكم
                    </a>
                </li>
                <li>
                    <a href="{{ route('forms.index') }}" class="font-semibold text-slate-700 hover-psau {{ request()->is('forms*') ? 'bg-slate-100 text-psau-teal' : '' }}">
                        📝 إدارة النماذج
                    </a>
                </li>
            </ul>
            
            <div class="text-center text-[10px] text-slate-400 border-t pt-3 border-slate-100 font-semibold">
                عمادة شؤون الطلاب &copy; {{ date('Y') }}
            </div>
        </aside>

        <main class="flex-1 min-w-0 overflow-hidden">
            @if(session('success'))
                <div class="alert alert-success shadow-sm mb-6 rounded-xl text-sm font-semibold">
                    <div>✓ {{ session('success') }}</div>
                </div>
            @endif

            @yield('content')
        </main>

    </div>

</body>
</html>