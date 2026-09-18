<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم التسجيل بنجاح</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2 family=Tajawal:wght@700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Tajawal', sans-serif; }</style>
</head>
<body class="bg-white min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center border-2 border-black p-8 rounded-2xl bg-white shadow-sm">
        <div class="text-6xl mb-4">✅</div>
        <h1 class="text-2xl font-bold text-black mb-3">تم إرسال طلبك بنجاح!</h1>
        <p class="text-slate-800 font-medium text-sm leading-relaxed mb-6">
            شكرًا لك، تم تسجيل بياناتك بنجاح في استمارة: <br>
            <span class="font-bold text-black text-base">"{{ $form->title }}"</span>
        </p>
        <div class="border-t-2 border-black pt-4 text-xs font-bold text-slate-600">
            جامعة الأمير سطام بن عبد العزيز
        </div>
    </div>
</body>
</html>