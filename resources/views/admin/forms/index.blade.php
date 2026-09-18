@extends('layouts.admin')

@section('title', 'إدارة النماذج')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">إدارة النماذج والأنشطة 📝</h1>
            <p class="text-sm text-slate-500 mt-1">هنا تجد جميع النماذج التي قمت بتصميمها، ويمكنك متابعة ردود الطلاب وتعديل الحالات.</p>
        </div>
        <a href="{{ route('forms.create') }}" class="btn bg-psau-teal text-white hover:opacity-90 border-none px-6 shadow-md">
            + تصميم نموذج جديد
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($forms->isEmpty())
            <div class="text-center py-16 px-4">
                <div class="text-5xl mb-4">📋</div>
                <h3 class="text-lg font-bold text-slate-700">لا توجد نماذج مصممة حالياً</h3>
                <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">ابدأ بالضغط على زر "تصميم نموذج جديد" لبناء أول نموذج ديناميكي للطلاب.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table w-full text-right">
                    <thead class="bg-slate-50 text-slate-600 font-bold text-sm border-b border-slate-200">
                        <tr>
                            <th class="p-4">عنوان النموذج</th>
                            <th class="p-4 text-center">الحالة</th>
                            <th class="p-4 text-center">عدد المسجلين</th>
                            <th class="p-4">تاريخ الإنشاء</th>
                            <th class="p-4 text-center">التحكم والعمليات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($forms as $form)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="p-4 max-w-xs">
                                    <div class="font-bold text-slate-800 truncate">{{ $form->title }}</div>
                                    <div class="text-xs text-slate-400 truncate mt-0.5">{{ $form->description ?? 'لا يوجد وصف لحق بهذا النموذج.' }}</div>
                                    
                                    <div class="mt-2 flex items-center gap-2">
                                        <input type="text" id="url-{{ $form->uuid }}" class="hidden" value="{{ url('/s/forms/' . $form->uuid) }}">
                                        <button onclick="copyFormUrl('{{ $form->uuid }}')" class="btn btn-xs bg-slate-100 hover:bg-psau-teal hover:text-white border-none text-slate-600 transition-all rounded px-2 py-0.5 flex items-center gap-1">
                                            🔗 <span id="btn-text-{{ $form->uuid }}">نسخ رابط التقديم</span>
                                        </button>
                                    </div>
                                </td>
                                
                                <td class="p-4 text-center">
                                    @if($form->status === 'published')
                                        <span class="badge badge-success bg-emerald-50 text-emerald-700 border-emerald-200 px-3 py-2 font-semibold text-xs">نشط (متاح)</span>
                                    @else
                                        <span class="badge badge-ghost bg-slate-100 text-slate-500 border-slate-200 px-3 py-2 font-semibold text-xs">مسودة</span>
                                    @endif
                                </td>
                                
                                <td class="p-4 text-center font-bold text-slate-700">
                                    <span class="bg-slate-100 text-slate-800 px-2.5 py-1 rounded-lg text-xs">
                                        {{ $form->submissions_count }} طالب
                                    </span>
                                </td>
                                
                                <td class="p-4 text-xs text-slate-500 font-medium">
                                    {{ $form->created_at->format('Y-m-d') }}
                                </td>
                                
                                <td class="p-4 text-center space-x-1 space-x-reverse">
                                    <a href="/forms/{{$form->id}}/submissions" class="btn btn-ghost btn-xs text-psau-teal font-bold hover:bg-teal-50">
                                        الردود
                                    </a>
                                    <a href="{{ route('forms.show', $form->id) }}" class="btn btn-ghost btn-xs text-psau-teal font-bold hover:bg-teal-50">
                                        👁️ عرض
                                    </a>
                                    
                                    
                                    <a href="{{ route('forms.edit', $form->id) }}" class="btn btn-ghost btn-xs text-blue-600 font-bold hover:bg-blue-50">
                                        ✏️ تعديل
                                    </a>

                                    <form action="{{ route('forms.destroy', $form->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا النموذج نهائياً؟ سيتم حذف جميع ردود الطلاب التابعة له أيضاً!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-xs text-red-500 font-bold hover:bg-red-50">
                                            🗑️ حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
<script>
function copyFormUrl(uuid) {
    console.log('hh');
    // جلب القيمة من الحقل المخفي
    const urlValue = document.getElementById('url-' + uuid).value;
    console.log(urlValue);
    
    // التحقق أولاً إذا كان المتصفح يدعم الطريقة الحديثة (https)
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(urlValue)
            .then(() => handleSuccessCopy(uuid))
            .catch(err => handleFallbackCopy(urlValue, uuid));
    } else {
        // إذا كان الرابط http غير مشفر، يتم الانتقال للطريقة البديلة فوراً
        handleFallbackCopy(urlValue, uuid);
    }
}

// دالة لتغيير نص الزر عند نجاح النسخ
function handleSuccessCopy(uuid) {
    const btnText = document.getElementById('btn-text-' + uuid);
    const originalText = btnText.innerText;
    btnText.innerText = "تم النسخ بنجاح! ✓";
    
    // لإضافة تأثير لوني متناسق مع الداشبرد
    btnText.parentElement.classList.add('bg-emerald-500', 'text-white');
    
    setTimeout(() => {
        btnText.innerText = originalText;
        btnText.parentElement.classList.remove('bg-emerald-500', 'text-white');
    }, 2000);
}

// الطريقة البديلة للعمل على بيئات الـ http والروابط المحلية غير المشفرة
function handleFallbackCopy(text, uuid) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // إخفاء عنصر النص خارج الشاشة تماماً لئلا يفسد مظهر الصفحة
    textArea.style.position = "fixed";
    textArea.style.top = "-9999px";
    document.body.appendChild(textArea);
    
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            handleSuccessCopy(uuid);
        } else {
            alert('فشل النسخ التلقائي، يرجى نسخه يدوياً: ' + text);
        }
    } catch (err) {
        alert('حدث خطأ أثناء النسخ، يرجى نسخه يدوياً: ' + text);
    }
    
    document.body.removeChild(textArea);
}
</script>
@endsection