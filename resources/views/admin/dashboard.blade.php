@extends('layouts.admin')

@section('title', 'الرئيسية')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">أهلاً بك في لوحة تحكم الأنشطة 🎓</h1>
            <p class="text-sm text-slate-500 mt-1">من هنا يمكنك إدارة النماذج الإلكترونية، تتبع أعداد المسجلين، واستخراج البيانات للطلاب.</p>
        </div>
        <a href="{{ route('forms.create') }}" class="btn bg-psau-teal text-white hover:opacity-90 border-none px-6">
            + إنشاء نموذج جديد
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <div class="card bg-white border border-slate-200 shadow-sm">
            <div class="card-body p-5 flex flex-row items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-400">إجمالي النماذج</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $totalForms ?? 0 }}</h3>
                </div>
                <div class="text-3xl p-3 bg-slate-100 rounded-xl">📝</div>
            </div>
        </div>

        <div class="card bg-white border border-slate-200 shadow-sm">
            <div class="card-body p-5 flex flex-row items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-400">النماذج النشطة حالياً</p>
                    <h3 class="text-3xl font-black text-emerald-600 mt-1">{{ $activeForms ?? 0 }}</h3>
                </div>
                <div class="text-3xl p-3 bg-emerald-50 rounded-xl">🟢</div>
            </div>
        </div>

        <div class="card bg-white border border-slate-200 shadow-sm">
            <div class="card-body p-5 flex flex-row items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-slate-400">إجمالي تسجيلات الطلاب</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $totalSubmissions ?? 0 }}</h3>
                </div>
                <div class="text-3xl p-3 bg-slate-100 rounded-xl">👥</div>
            </div>
        </div>

    </div>
</div>
@endsection