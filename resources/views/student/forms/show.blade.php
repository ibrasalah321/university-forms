<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@1.16.2/dist/full.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet"> 
    <style> 
        body {  
            font-family: 'Tajawal', sans-serif;
            color: #111827;
        } 

        /* الهوية الرسمية لجامعة الأمير سطام */
        .bg-psau-navy { 
            background-color: #0d2746; 
        } 

        .focus-psau:focus {  
            border-color: #0d2746 !important;  
            outline: 3px solid rgba(13, 39, 70, 0.20); 
        }

        /* جعل النصوص والـ Labels أكثر وضوحاً */
        .label-text {
            color: #111827 !important;
        }

        .label-text-alt {
            color: #374151 !important;
        }

        input::placeholder,
        textarea::placeholder {
            color: #6b7280 !important;
            opacity: 1;
        }

        select {
            color: #111827 !important;
        }
    </style> 
</head> 
<body class="bg-white min-h-screen py-10 px-4"> 
 
    <div class="max-w-2xl mx-auto bg-white"> 
         
        <div class="text-center mb-8 border-b-2 border-gray-800 pb-6 bg-white"> 
            <div class="flex justify-center mb-4"> 
                <img src="{{ asset('images/psau-logo.png') }}" alt="شعار جامعة الأمير سطام" class="h-24 w-auto object-contain"> 
            </div> 
            <h2 class="text-2xl font-bold text-gray-900">جامعة الأمير سطام بن عبد العزيز</h2> 
            <p class="text-sm font-bold text-gray-700 mt-1 tracking-wider">بوابة المشاركة والتسجيل في الأنشطة والمسابقات</p> 
        </div> 
 
        <div class="mb-8 bg-white p-5 rounded-xl border-2 border-gray-800"> 
            <h1 class="text-2xl font-bold text-gray-900 mb-2"> 
                {{ $form->title }} 
            </h1> 
            @if($form->description) 
                <div class="text-gray-800 text-sm font-medium leading-relaxed whitespace-pre-line border-t-2 border-gray-700 pt-3 mt-3"> 
                    {{ $form->description }} 
                </div> 
            @endif 
        </div> 
 
        @if ($errors->any()) 
            <div class="p-4 bg-red-50 border-2 border-red-600 rounded-xl mb-6 text-sm text-red-800 font-bold"> 
                <div class="mb-1 flex items-center gap-1">⚠️ يرجى تصحيح التنبيهات التالية لإتمام الطلب:</div> 
                <ul class="list-disc list-inside text-xs font-bold space-y-1 mt-2 text-red-700"> 
                    @foreach ($errors->all() as $error) 
                        <li>{{ $error }}</li> 
                    @endforeach 
                </ul> 
            </div> 
        @endif 
 
        <form action="{{ route('student.forms.submit', $form->uuid) }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white"> 
            @csrf 
 
            <div class="space-y-6 bg-white"> 
                @foreach($form->fields as $field) 
                    <div class="form-control w-full bg-white"> 
                        <label class="label pt-0 pb-1.5"> 
                            <span class="label-text font-bold text-gray-900 text-base"> 
                                {{ $field->label }}  
                                @if($field->is_required) <span class="text-red-600 font-bold text-lg">*</span> @endif 
                            </span> 
                        </label> 
 
                        @if(in_array($field->field_type, ['text', 'number', 'email', 'tel'])) 
                            <input type="{{ $field->field_type }}"  
                                   name="answers[{{ $field->id }}]"  
                                   class="input input-bordered w-full bg-white border-2 border-gray-700 text-gray-900 font-bold rounded-lg shadow-sm focus-psau transition-all"  
                                   placeholder="{{ $field->placeholder ?? 'أدخل ' . $field->label }}" 
                                   {{ $field->is_required ? 'required' : '' }}> 
 
                        @elseif($field->field_type === 'textarea') 
                            <textarea name="answers[{{ $field->id }}]"  
                                      class="textarea textarea-bordered h-28 w-full bg-white border-2 border-gray-700 text-gray-900 font-bold rounded-lg shadow-sm focus-psau transition-all"  
                                      placeholder="{{ $field->placeholder ?? 'اكتب هنا تفاصيل الإجابة...' }}" 
                                      {{ $field->is_required ? 'required' : '' }}></textarea> 
 
                        @elseif($field->field_type === 'select') 
                            <select name="answers[{{ $field->id }}]"  
                                    class="select select-bordered w-full bg-white border-2 border-gray-700 text-gray-900 font-bold rounded-lg shadow-sm focus-psau transition-all" 
                                    {{ $field->is_required ? 'required' : '' }}> 
                                <option value="" disabled selected class="text-gray-600">-- اضغط للاختيار من القائمة --</option> 
                                @if(is_array($field->options)) 
                                    @foreach($field->options as $option) 
                                        <option value="{{ $option }}" class="text-gray-900 font-bold">{{ $option }}</option> 
                                    @endforeach 
                                @endif 
                            </select> 
 
                        @elseif($field->field_type === 'file') 
                            <div class="border-2 border-gray-700 rounded-lg p-2 bg-white"> 
                                <input type="file"  
                                       name="answers[{{ $field->id }}]"  
                                       class="file-input file-input-bordered w-full bg-white border border-gray-700 text-gray-900 font-bold rounded-md shadow-sm" 
                                       {{ $field->is_required ? 'required' : '' }}> 
                            </div> 
                        @endif 
 
                        @if($field->help_text) 
                            <label class="label py-1"> 
                                <span class="label-text-alt text-gray-700 text-xs font-bold">📌 {{ $field->help_text }}</span> 
                            </label> 
                        @endif 
                    </div> 
                @endforeach 
            </div> 
 
            <div class="pt-4"> 
                <button type="submit" class="btn bg-psau-navy hover:bg-gray-900 text-white w-full border-none rounded-lg text-lg font-bold shadow-md transition-all py-3"> 
                    إرسال الطلب وإتمام التسجيل 
                </button> 
            </div> 
        </form> 
         
        <div class="text-center mt-12 pt-4 border-t-2 border-gray-800 text-gray-800 text-xs font-bold"> 
            تم البرمجة بواسطة ابراهيم صلاح 
        </div> 
    </div> 
 
</body> 
</html>