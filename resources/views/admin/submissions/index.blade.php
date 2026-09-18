@extends('layouts.admin')

@section('title', 'إدارة الردود - ' . $form->title)

@section('content')
<div class="w-full max-w-full min-w-0 overflow-hidden space-y-6 px-2" dir="rtl">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50 p-6 rounded-2xl border border-slate-200 shadow-sm w-full">
        <div>
            <h1 class="text-xl font-bold text-slate-800">{{ $form->title }}</h1>
            <p class="text-xs text-slate-500 mt-1">إجمالي الردود المستلمة: <span class="font-bold text-emerald-600">{{ $submissionsCount}}</span></p>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{route('forms.submissions.export',$form->id)}}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl transition shadow-sm whitespace-nowrap">
                📁 تصدير إلى Excel
            </a>

        </div>
    </div>

<div class="w-full max-w-full overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-sm">
    
    <div class="w-full max-w-full overflow-x-auto block">
        
        <table class="w-full border-collapse text-right table-auto mb-6">
            
            <colgroup>
                <col style="min-width: 140px; width: 140px;">
                @foreach($form->fields as $field)
                    <col style="min-width: 180px; width: 180px;">
                @endforeach
            </colgroup>

            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-600 tracking-wide whitespace-nowrap">
                        Submitted at
                    </th>
                    @foreach($form->fields as $field)
                        <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-600 tracking-wide whitespace-nowrap">
                            {{ $field->label }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($submissions as $submission)
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        <td class="px-6 py-4 text-xs text-slate-500 font-medium whitespace-nowrap">
                            {{ $submission->created_at->format('M d, h:i A') }}
                        </td>
                        
                        @foreach($form->fields as $field)
                            @php
                                $submissionValue = $submission->values->where('field_id', $field->id)->first();
                                $value = $submissionValue ? $submissionValue->value : null;
                            @endphp
                            <td class="px-6 py-4 text-xs text-slate-700 whitespace-nowrap">
                                @if(empty($value))
                                    <span class="text-slate-400 italic">لا توجد إجابة</span>
                                @elseif($field->field_type === 'file')
                                    <a href="{{ route('forms.submissions.file', $submissionValue->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                        📄 استعراض المرفق
                                    </a>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $form->fields->count() + 1 }}" class="px-6 py-12 text-center text-slate-400 text-xs font-medium">
                            📭 لا توجد أي ردود مرسلة لهذا النموذج حتى الآن.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{$submissions->links()}}

    </div>
</div>

@endsection