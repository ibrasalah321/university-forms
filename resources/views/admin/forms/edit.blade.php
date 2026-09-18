@extends('layouts.admin')
@section('title', 'تعديل النموذج ' . $form->title)
@section('content')

    <div class="max-w-4xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">تعديل النموذج ({{$form->title}})</h1>
            <a href="{{ route('forms.index') }}" class="btn btn-outline btn-sm">إلغاء والعودة</a>
        </div>

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-6 text-sm text-red-700 font-bold">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('forms.update', $form->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="card bg-white shadow-sm border border-slate-200">
                <div class="card-body p-6">
                    <h2 class="text-lg font-bold text-psau-teal mb-4 border-b pb-2">1. إعدادات النموذج الأساسية</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-control w-full md:col-span-2">
                            <label class="label"><span class="label-text font-bold text-slate-700">عنوان النموذج *</span></label>
                            <input value="{{ old('title', $form->title) }}" type="text" name="title" class="input input-bordered w-full bg-slate-50 focus:border-psau-teal" placeholder="مثال: التسجيل في هكاثون التمكين الفكري" required>
                        </div>

                        <div class="form-control w-full md:col-span-2">
                            <label class="label"><span class="label-text font-bold text-slate-700">وصف أو شروط النموذج</span></label>
                            <textarea name="description" class="textarea textarea-bordered h-24 bg-slate-50 focus:border-psau-teal" placeholder="اكتب هنا تفاصيل النشاط...">{{ old('description', $form->description) }}</textarea>
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-slate-700">تاريخ ووقت البدء</span></label>
                            <input value="{{ $form->start_at ? \Carbon\Carbon::parse($form->start_at)->format('Y-m-d\TH:i') : '' }}" type="datetime-local" name="start_at" class="input input-bordered w-full bg-slate-50">
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-slate-700">تاريخ ووقت الانتهاء</span></label>
                            <input value="{{ $form->end_at ? \Carbon\Carbon::parse($form->end_at)->format('Y-m-d\TH:i') : '' }}" type="datetime-local" name="end_at" class="input input-bordered w-full bg-slate-50">
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-slate-700">الحد الأقصى للتسجيل (اختياري)</span></label>
                            <input value="{{ old('max_submissions', $form->max_submissions) }}" type="number" name="max_submissions" min="1" class="input input-bordered w-full bg-slate-50" placeholder="مثال: 50">
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold text-slate-700">حالة النموذج</span></label>
                            <select name="status" class="select select-bordered w-full bg-slate-50">
                                <option value="published" {{ old('status', $form->status) === 'published' ? 'selected' : '' }}>نشط (متاح للطلاب فوراً)</option>
                                <option value="draft" {{ old('status', $form->status) === 'draft' ? 'selected' : '' }}>مسودة (مخفي حالياً)</option>
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
                        @foreach($form->fields as $index => $field)
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 relative field-row space-y-3">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                                    <div class="form-control md:col-span-3">
                                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">اسم الحقل</span></label>
                                        <input type="text" name="fields[{{ $index }}][label]" value="{{ $field->label }}" class="input input-sm input-bordered w-full bg-white" placeholder="أدخل المسمى" required>
                                    </div>

                                    <div class="form-control md:col-span-3">
                                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">نوع الإدخال</span></label>
                                        <select name="fields[{{ $index }}][field_type]" class="select select-sm select-bordered w-full bg-white field-type-selector" onchange="toggleOptionsInput(this, {{ $index }})">
                                            <option value="text" {{ $field->field_type === 'text' ? 'selected' : '' }}>نص قصير (Text)</option>
                                            <option value="number" {{ $field->field_type === 'number' ? 'selected' : '' }}>رقم (Number)</option>
                                            <option value="email" {{ $field->field_type === 'email' ? 'selected' : '' }}>بريد إلكتروني (Email)</option>
                                            <option value="tel" {{ $field->field_type === 'tel' ? 'selected' : '' }}>رقم هاتف (Phone)</option>
                                            <option value="textarea" {{ $field->field_type === 'textarea' ? 'selected' : '' }}>نص طويل (Textarea)</option>
                                            <option value="file" {{ $field->field_type === 'file' ? 'selected' : '' }}>رفع ملف (File Upload)</option>
                                            <option value="select" {{ $field->field_type === 'select' ? 'selected' : '' }}>قائمة خيارات (Dropdown Select)</option>
                                        </select>
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">نص مؤقت (Placeholder)</span></label>
                                        <input type="text" name="fields[{{ $index }}][placeholder]" value="{{ $field->placeholder }}" class="input input-sm input-bordered w-full bg-white" placeholder="مثال: 44XXXXXXXX">
                                    </div>

                                    <div class="form-control md:col-span-2">
                                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">نص توضيحي</span></label>
                                        <input type="text" name="fields[{{ $index }}][help_text]" value="{{ $field->help_text }}" class="input input-sm input-bordered w-full bg-white" placeholder="ملاحظة تحت الحقل">
                                    </div>

                                    <div class="md:col-span-1 flex flex-col items-center justify-center pb-2">
                                        <span class="text-[10px] text-slate-400 font-bold mb-1">خيارات الحقل</span>
                                        <div class="flex gap-2">
                                            <label class="cursor-pointer" title="حقل مطلوب">
                                                <input type="checkbox" name="fields[{{ $index }}][is_required]" value="1" {{ $field->is_required ? 'checked' : '' }} class="checkbox checkbox-xs checkbox-primary" />
                                            </label>
                                            <label class="cursor-pointer" title="منع التكرار">
                                                <input type="checkbox" name="fields[{{ $index }}][unique_check]" value="1" {{ $field->unique_check ? 'checked' : '' }} class="checkbox checkbox-xs checkbox-secondary" />
                                            </label>
                                        </div>
                                    </div>

                                    <div class="md:col-span-1 text-left">
                                        <button type="button" class="btn btn-error btn-sm btn-square text-white remove-field-btn">✕</button>
                                    </div>
                                </div>

                                <div id="options-container-{{ $index }}" style="display: {{ $field->field_type === 'select' ? 'flex' : 'none' }}" class="form-control w-full border-t pt-2 border-dashed border-slate-200">
                                    <label class="label py-1"><span class="label-text text-xs font-bold text-slate-500">خيارات القائمة (افصل بين كل خيار بـ فاصلة ,)</span></label>
                                    <input type="text" name="fields[{{ $index }}][options]" value="{{ is_array($field->options) ? implode(',', $field->options) : '' }}" class="input input-sm input-bordered w-full bg-white" placeholder="خيارات المفاضلة...">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn bg-psau-teal hover:opacity-90 text-white px-8 btn-md text-lg font-bold border-none shadow-md">
                    حفظ ونشر التعديلات
                </button>
            </div>
        </form>
    </div>

<script>
    let fieldIndex = @js($form->fields->count()); 

    document.getElementById('add-field-btn').addEventListener('click', function() {
        const container = document.getElementById('fields-container');
        
        // إزالة أكواد الـ Blade البرمجية غير المتوافقة مع المحاكاة الديناميكية من قالب الـ JS الجديد
        const fieldHTML = `
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 relative field-row space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    
                    <div class="form-control md:col-span-3">
                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">اسم الحقل</span></label>
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
                            <option value="file">رفع ملف (File Upload)</option>
                            <option value="select">قائمة خيارات (Dropdown Select)</option>
                        </select>
                    </div>

                    <div class="form-control md:col-span-2">
                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">نص مؤقت (Placeholder)</span></label>
                        <input type="text" name="fields[${fieldIndex}][placeholder]" class="input input-sm input-bordered w-full bg-white" placeholder="مثال: 44XXXXXXXX">
                    </div>

                    <div class="form-control md:col-span-2">
                        <label class="label py-1"><span class="label-text text-xs font-bold text-slate-600">نص مساعد للطلاب</span></label>
                        <input type="text" name="fields[${fieldIndex}][help_text]" class="input input-sm input-bordered w-full bg-white" placeholder="ملاحظة تحت الحقل...">
                    </div>

                    <div class="md:col-span-1 flex flex-col items-center justify-center pb-2">
                        <span class="text-[10px] text-slate-400 font-bold mb-1">خيارات</span>
                        <div class="flex gap-2">
                            <label class="cursor-pointer" title="حقل مطلوب">
                                <input type="checkbox" name="fields[${fieldIndex}][is_required]" value="1" class="checkbox checkbox-xs checkbox-primary" />
                            </label>
                            <label class="cursor-pointer" title="منع التكرار">
                                <input type="checkbox" name="fields[${fieldIndex}][unique_check]" value="1" class="checkbox checkbox-xs checkbox-secondary" />
                            </label>
                        </div>
                    </div>

                    <div class="md:col-span-1 text-left">
                        <button type="button" class="btn btn-error btn-sm btn-square text-white remove-field-btn">✕</button>
                    </div>
                </div>

                <div id="options-container-${fieldIndex}" style="display:none" class="form-control w-full border-t pt-2 border-dashed border-slate-200">
                    <label class="label py-1"><span class="label-text text-xs font-bold text-slate-500">خيارات القائمة (افصل بين كل خيار بـ فاصلة ,)</span></label>
                    <input type="text" name="fields[${fieldIndex}][options]" class="input input-sm input-bordered w-full bg-white" placeholder="مثال: خيار 1, خيار 2">
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', fieldHTML);
        fieldIndex++;
    });

    function toggleOptionsInput(selectElement, index) {
        const optionsContainer = document.getElementById(`options-container-${index}`);
        if (selectElement.value === 'select') {
            optionsContainer.style.display = 'flex';
        } else {
            optionsContainer.style.display = 'none';
        }
    }

    document.getElementById('fields-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-field-btn')) {
            e.target.closest('.field-row').remove();
        }
    });
</script>

@endsection