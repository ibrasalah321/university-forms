@extends('layouts.admin')
@section('title', 'إنشاء نموذج جديد')
@section('content')



    <div class="max-w-4xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">تصميم نموذج أنشطة جديد</h1>
            <a href="{{ route('forms.index') }}" class="btn btn-outline btn-sm">إلغاء والعودة</a>
        </div>

        <form action="{{ route('forms.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="card bg-white shadow-sm border border-slate-200">
                <div class="card-body p-6">
                    <h2 class="text-lg font-bold text-psau-teal mb-4 border-b pb-2">1. إعدادات النموذج الأساسية</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control w-full md:col-span-2">
                            <label class="label"><span class="label-text font-bold text-slate-700">عنوان النموذج *</span></label>
                            <input type="text" name="title" class="input input-bordered w-full bg-slate-50 focus:border-psau-teal" placeholder="مثال: التسجيل في هكاثون التمكين الفكري" required>
                        </div>

                        <div class="form-control w-full md:col-span-2">
                            <label class="label"><span class="label-text font-bold text-slate-700">وصف أو شروط النموذج</span></label>
                            <textarea name="description" class="textarea textarea-bordered h-24 bg-slate-50 focus:border-psau-teal" placeholder="اكتب هنا تفاصيل النشاط أو الشروط والأحكام للطلاب..."></textarea>
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-slate-700">تاريخ ووقت البدء</span></label>
                            <input type="datetime-local" name="start_at" class="input input-bordered w-full bg-slate-50">
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-slate-700">تاريخ ووقت الانتهاء</span></label>
                            <input type="datetime-local" name="end_at" class="input input-bordered w-full bg-slate-50">
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-slate-700">الحد الأقصى للتسجيل (اختياري)</span></label>
                            <input type="number" name="max_submissions" min="1" class="input input-bordered w-full bg-slate-50" placeholder="مثال: 50 طالب فقط">
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-slate-700">حالة النموذج المبدئية</span></label>
                            <select name="status" class="select select-bordered w-full bg-slate-50">
                                <option value="published">نشط (متاح للطلاب فوراً)</option>
                                <option value="draft">مسودة (مخفي لتعديله لاحقاً)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-white shadow-sm border border-slate-200">
                <div class="card-body p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-lg font-bold text-psau-teal">2. حقول النموذج (البيانات المطلوبة من الطالب)</h2>
                        <button type="button" id="add-field-btn" class="btn bg-psau-teal text-white hover:opacity-90 btn-sm">
                            + إضافة حقل جديد
                        </button>
                    </div>

                    <div id="fields-container" class="space-y-4">
                        </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn bg-psau-teal hover:opacity-90 text-white px-8 btn-md text-lg font-bold border-none shadow-md">
                    حفظ ونشر النموذج
                </button>
            </div>
        </form>
    </div>

<script>
    let fieldIndex = 0;

    document.getElementById('add-field-btn').addEventListener('click', function() {
        const container = document.getElementById('fields-container');
        
        // قالب الحقل الديناميكي المحدث
        const fieldHTML = `
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 relative field-row space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    
                    <div class="form-control md:col-span-4">
                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">اسم الحقل (مثال: الكلية، السيرة الذاتية)</span></label>
                        <input type="text" name="fields[${fieldIndex}][label]" class="input input-sm input-bordered w-full bg-white" placeholder="أدخل المسمى" required>
                    </div>

                    <div class="form-control md:col-span-3">
                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">نوع الإدخال</span></label>
                        <select name="fields[${fieldIndex}][field_type]" class="select select-sm select-bordered w-full bg-white field-type-selector" onchange="toggleOptionsInput(this, ${fieldIndex})">
                            <option value="text">نص قصير (Text)</option>
                            <option value="number">رقم (Number)</option>
                            <option value="email">بريد إلكتروني (Email)</option>
                            <option value="tel">رقم هاتف (Phone)</option>
                            <option value="textarea">نص طويل (Textarea)</option>
                            <option value="file">رفع ملف / وثيقة (File Upload)</option>
                            <option value="select">قائمة خيارات منسدلة (Dropdown Select)</option>
                        </select>
                    </div>
                    <div class="form-control md:col-span-3">
                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">نص مؤقت (Placeholder)</span></label>
                        <input type="text" name="fields[${fieldIndex}][placeholder]" class="input input-sm input-bordered w-full bg-white" placeholder="مثال: 44XXXXXXXX">
                    </div>

                    <div class="form-control md:col-span-3">
                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">نص مساعد للطلاب</span></label>
                        <input type="text" name="fields[${fieldIndex}][help_text]" class="input input-sm input-bordered w-full bg-white" placeholder="ملاحظة تظهر تحت الحقل...">
                    </div>

                    <div class="md:col-span-4 flex gap-4 pb-2 justify-start md:justify-center">
                        <label class="cursor-pointer label gap-2 py-0">
                            <input type="checkbox" name="fields[${fieldIndex}][is_required]" value="1" class="checkbox checkbox-xs checkbox-primary" />
                            <span class="label-text text-xs font-semibold text-slate-600">حقل مطلوب</span>
                        </label>
                        
                        <label class="cursor-pointer label gap-2 py-0">
                            <input type="checkbox" name="fields[${fieldIndex}][unique_check]" value="1" class="checkbox checkbox-xs checkbox-secondary" />
                            <span class="label-text text-xs font-semibold text-slate-600 text-error">منع التكرار</span>
                        </label>
                    </div>

                    <div class="md:col-span-1 text-left">
                        <button type="button" class="btn btn-error btn-sm btn-square text-white remove-field-btn">
                            ✕
                        </button>
                    </div>
                </div>

                <div id="options-container-${fieldIndex}" style="display:none" class="form-control w-full hidden border-t pt-2 border-dashed border-slate-200">
                    <label class="label py-1"><span class="label-text text-xs font-bold text-slate-500">أدخل خيارات القائمة (افصل بين كل خيار بـ فاصلة ,)</span></label>
                    <input type="text" name="fields[${fieldIndex}][options]" class="input input-sm input-bordered w-full bg-white" placeholder="مثال: هندسة الحاسب, الهندسة الكهربائية, هندسة البرمجيات">
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', fieldHTML);
        fieldIndex++;
    });

    // دالة سحرية لإظهار وإخفاء حقل الخيارات ديناميكياً
    function toggleOptionsInput(selectElement, index) {
        const optionsContainer = document.getElementById(`options-container-${index}`);
        const optionsInput = optionsContainer.querySelector('input');
        
        if (selectElement.value === 'select') {
            optionsContainer.style.display = 'flex'
        } else {
            optionsContainer.style.display = 'none';
        }
    }

    // تفعيل زر حذف الحقل عند الضغط عليه
    document.getElementById('fields-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-field-btn')) {
            e.target.closest('.field-row').remove();
        }
    });

    // توليد حقل أول تلقائي عند فتح الصفحة
    document.getElementById('add-field-btn').click();

</script>

@endsection