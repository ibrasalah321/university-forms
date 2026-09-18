@extends('layouts.admin')
@section('title', 'عرض النموذج')
@section('content')


<div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
    <h1 class="text-2xl font-bold text-slate-800 mb-4">عرض النموذج: {{ $form->title }}</h1>
    <p class="text-sm text-slate-500 mb-6">{{ $form->description ?? 'لا يوجد وصف لهذا النموذج.' }}</p>

    <div class="space-y-6 bg-white">
        @foreach($form->fields as $field)
            <div class="form-control w-full bg-white">
                <label class="label pt-0 pb-1.5">
                    <span class="label-text font-bold text-black text-base">
                        {{ $field->label }} 
                        @if($field->is_required) <span class="text-red-600 font-bold text-lg">*</span> @endif
                    </span>
                </label>
                @if(in_array($field->field_type, ['text', 'number', 'email', 'tel']))
                    <input type="{{ $field->field_type }}" 
                           class="input input-bordered w-full bg-white border-2 border-black text-black font-bold rounded-lg shadow-sm focus-psau transition-all" 
                           placeholder="{{ $field->placeholder ?? 'أدخل ' . $field->label }}"
                           disabled>
                @elseif($field->field_type === 'textarea')
                    <textarea class="textarea textarea-bordered h-28 w-full bg-white border-2 border-black text-black font-bold rounded-lg shadow-sm focus-psau transition-all" 
                              placeholder="{{ $field->placeholder ?? 'اكتب هنا تفاصيل الإجابة...' }}"
                              disabled></textarea>
                @elseif($field->field_type === 'select')
                    <select class="select select-bordered w-full bg-white border-2 border-black text-black font-bold rounded-lg shadow-sm focus-psau transition-all"
                            disabled>
                        <option value="" disabled selected class="text-slate-500">-- اضغط للاختيار من القائمة --</option>
                        @if(is_array($field->options))
                            @foreach($field->options as $option)
                                <option value="{{ $option }}" class="text-black font-bold">{{ $option }}</option>
                            @endforeach
                        @endif
                    </select>
                @elseif($field->field_type === 'file')
                    <div class="border-2 border-black rounded-lg p-2 bg-white">
                        <input type="file" 
                               class="file-input file-input-bordered w-full bg-white border border-slate-900 text-black font-bold rounded-md shadow-sm"
                               disabled>
                    </div>
                @endif
                @if($field->help_text)
                    <label class="label py-1">
                        <span class="label-text-alt text-black text-xs font-bold">📌 {{ $field->help_text }}</span>
                    </label>
                @endif
            </div>
        @endforeach
    </div>

















@endsection