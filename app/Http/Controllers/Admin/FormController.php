<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // جلب جميع النماذج المرتبطة بالأدمن الحالي مع حساب عدد الردود تلقائياً لكل نموذج
    // الاستعلام بجلب (withCount('submissions')) يوفر استعلامات منفصلة ويسرع النظام جداً
    $forms = Form::where('created_by',Auth::id())
                 ->withCount('submissions') 
                 ->orderBy('created_at', 'desc')
                 ->get();

    // تمرير النماذج المجلوبة إلى صفحة الـ Blade لعرضها في الجدول
    return view('admin.forms.index', compact('forms'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.forms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'max_submissions' => 'nullable|integer|min:1',
        'start_at' => 'nullable|date',
        'end_at' => 'nullable|date|after_or_equal:start_at',
        'status' => 'required|in:published,draft',

        'fields' => 'required|array|min:1',
        'fields.*.label' => 'required|string|max:255',
        'fields.*.field_type' => 'required|in:text,number,email,tel,textarea,file,select',
        'fields.*.options' => 'nullable|string', // حقل الخيارات يكون مطلوباً فقط منطقياً عند اختيار select
    ]);

    $form = Form::create([
        'title' => $validatedData['title'],
        'description' => $validatedData['description'],
        'max_submissions' => $validatedData['max_submissions'],
        'start_at' => $validatedData['start_at'],
        'end_at' => $validatedData['end_at'],
        'status' => $validatedData['status'],
        'created_by' => Auth::id(), 
    ]);

    foreach ($request->fields as $index => $fieldData) {
        
        // معالجة الخيارات: إذا كتب الأدمن خيارات تفصل بينها فاصلة، نحولها لمصفوفة برمجية
        // مثال: "أ, ب, ج" تتحول إلى ["أ", "ب", "ج"] ليتم تخزينها كـ JSON في قاعدة البيانات
        $processedOptions = null;
        if ($fieldData['field_type'] === 'select' && !empty($fieldData['options'])) {
            // عمل تحويل للنص بناءً على الفاصلة وتنظيف الفراغات الزائدة
            $processedOptions = array_map('trim', explode(',', $fieldData['options']));
        }

        // إدخال الحقل في قاعدة البيانات عبر علاقة الـ Eloquent
        $form->fields()->create([
            'label' => $fieldData['label'], // تأكد هل اسم العمود في الـ Migration عندك label أو lable
            'field_type' => $fieldData['field_type'],
            'is_required' => isset($fieldData['is_required']) ? true : false,
            'unique_check' => isset($fieldData['unique_check']) ? true : false,
            'options' => $processedOptions, // سيتم تخزين المصفوفة تلقائياً كـ JSON إذا كنت مفعل الـ Cast في الموديل
            'placeholder' => $fieldData['placeholder'] ?? null,
            'help_text' => $fieldData['help_text'] ?? null,
            'sort_order' => $index, // ترتيب الحقل بناءً على ترتيبه في الواجهة
        ]);
    }

    // 4. إعادة التوجيه إلى الـ Dashboard مع رسالة نجاح
    return redirect()->route('dashboard')->with('success', 'تم إنشاء وتصميم النموذج بنجاح!');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $form = Form::with('fields')->findOrFail($id);
        return view('admin.forms.show', compact('form'));
    }

    public function edit(string $id)
{
    return view('admin.forms.edit', ['form' => Form::with('fields')->findOrFail($id)]);
}

public function update(Request $request, string $id)
{
    $form = Form::findOrFail($id);

    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'max_submissions' => 'nullable|integer|min:1',
        'start_at' => 'nullable|date',
        'end_at' => 'nullable|date|after_or_equal:start_at',
        'status' => 'required|in:published,draft',

        'fields' => 'required|array|min:1',
        'fields.*.label' => 'required|string|max:255',
        'fields.*.field_type' => 'required|in:text,number,email,tel,textarea,file,select',
        'fields.*.options' => 'nullable|string',
        'fields.*.placeholder' => 'nullable|string|max:255',
        'fields.*.help_text' => 'nullable|string|max:255',
    ]);

    // حماية عملية الحذف وإعادة البناء عبر Transaction
    DB::beginTransaction();

    try {
        $form->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'max_submissions' => $validatedData['max_submissions'],
            'start_at' => $validatedData['start_at'],
            'end_at' => $validatedData['end_at'],
            'status' => $validatedData['status'],
        ]);

        // حذف الحقول القديمة بأمان
        $form->fields()->delete();

        foreach ($request->fields as $index => $fieldData) {
            $processedOptions = null;
            if ($fieldData['field_type'] === 'select' && !empty($fieldData['options'])) {
                // تقسيم النص بفاصلة وتنظيف الفراغات حول الكلمات
                $processedOptions = array_map('trim', explode(',', $fieldData['options']));
            }

            $form->fields()->create([
                'label' => $fieldData['label'],
                'field_type' => $fieldData['field_type'],
                // استخدام منطق آمن للـ Checkboxes لمنع التجاهل عند إلغاء التحديد
                'is_required' => isset($fieldData['is_required']),
                'unique_check' => isset($fieldData['unique_check']),
                'options' => $processedOptions,
                'placeholder' => $fieldData['placeholder'] ?? null,
                'help_text' => $fieldData['help_text'] ?? null,
                'sort_order' => $index,
            ]);
        }

        DB::commit();
        return redirect()->route('forms.index')->with('success', 'تم تحديث النموذج وحقوله بنجاح!');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withInput()
            ->withErrors(['global' => 'حدث خطأ أثناء حفظ التعديلات: ' . $e->getMessage()]);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $form = Form::findOrFail($id);
        foreach($form->submissions as $submission){
            foreach($submission->values as $value){
                if($value->field->field_type === 'file' && $value->value){
                    // حذف الملف من التخزين
                    Storage::disk('public')->delete($value->value);
                }
            }
        }
        $form->delete();
        

        return redirect()->route('dashboard')->with('success', 'تم حذف النموذج بنجاح!');
    }
}
