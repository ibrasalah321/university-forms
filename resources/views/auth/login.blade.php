<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام النماذج الجامعية</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
        /* اللون الفيروزي الدقيق المستخرج من شعار الجامعة المرفق */
        .bg-psau-teal {
            background-color: #3c7974;
        }
        .text-psau-teal {
            color: #3c7974;
        }
        .border-psau-teal {
            border-color: #3c7974;
        }
        .input-psau:focus-within {
            border-color: #3c7974 !important;
            outline: 2px solid #3c7974;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="card bg-white w-full max-w-md shadow-2xl border-t-4 border-psau-teal">
        <div class="card-body p-8">
            
            <div class="flex flex-col items-center mb-6 text-center">
                <img src="{{ asset('images/psau-logo.png') }}" alt="شعار جامعة الأمير سطام" class="w-50 mb-6">
                    
                <h2 class="text-xl font-bold text-slate-800 mt-2">بوابة الإدارة الإلكترونية</h2>
                <p class="text-xs text-slate-500 mt-1">نظام إدارة نماذج الأنشطة الطلابية</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error shadow-sm mb-5 py-2 text-sm rounded-lg">
                    <div class="flex flex-col items-start">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2">
                                <span>•</span>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-4">
                @csrf

                <div class="form-control w-full">
                    <label class="label pt-0">
                        <span class="label-text font-bold text-slate-700">البريد الإلكتروني الجامعي</span>
                    </label>
                    <label class="input input-bordered flex items-center gap-2 input-psau bg-slate-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 text-psau-teal opacity-80"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                        <input type="email" name="email" value="{{ old('email') }}" class="grow text-left font-sans" placeholder="username@psau.edu.sa" required autofocus />
                    </label>
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text font-bold text-slate-700">كلمة المرور</span>
                    </label>
                    <label class="input input-bordered flex items-center gap-2 input-psau bg-slate-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 text-psau-teal opacity-80"><path fill-rule="evenodd" d="M14 6a4 4 0 0 1-4.899 3.899l-1.955 1.955a.5.5 0 0 1-.353.146H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2.293a.5.5 0 0 1 .146-.353l3.955-3.955A4 4 0 1 1 14 6Zm-4-2a.75.75 0 0 0 0 1.5.5.5 0 0 1 .5.5.75.75 0 0 0 1.5 0 2 2 0 0 0-2-2Z" clip-rule="evenodd" /></svg>
                        <input type="password" name="password" class="grow text-left font-sans" placeholder="••••••••" required />
                    </label>
                    
                </div>

                <div class="form-control mt-6">
                    <button type="submit" class="btn bg-psau-teal hover:opacity-90 text-white btn-block text-lg font-bold shadow-md transition-all border-none">
                        تسجيل الدخول
                    </button>
                </div>
            </form>

            <div class="divider text-xs text-slate-400 my-6 font-semibold">جامعة الأمير سطام بن عبد العزيز</div>

        </div>
    </div>

</body>
</html>