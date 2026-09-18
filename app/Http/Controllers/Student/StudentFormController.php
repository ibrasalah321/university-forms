<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Submission;
use App\Models\SubmissionValue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudentFormController extends Controller
{
    /**
     * عرض النموذج للطالب ديناميكياً
     */
    public function show($uuid)
    {
        $form = Form::where('uuid', $uuid)
                    ->with(['fields' => function($query) {
                        $query->orderBy('sort_order', 'asc');
                    }])
                    ->firstOrFail(); 

        if ($form->status !== 'published') {
            return abort(403, 'عذراً، هذا النموذج غير متاح للتسجيل حالياً (مسودة).');
        }

        $now = Carbon::now();
        if ($form->start_at && $now->lt(Carbon::parse($form->start_at))) {
            return abort(403, 'عذراً، التسجيل في هذا النشاط لم يبدأ بعد.');
        }
        
        if ($form->end_at && $now->gt(Carbon::parse($form->end_at))) {
            return abort(403, 'عذراً، تم إغلاق باب التسجيل في هذا النشاط لانتهاء الوقت المحدد.');
        }

        return view('student.forms.show', compact('form'));
    }

    // public function submit(Request $request, $uuid){
    //     $form = Form::where('uuid' , $uuid)->firstOrFail();
    //     if ($form->status !== 'published') {
    //         return abort(403, 'عذراً، هذا النموذج غير متاح للتسجيل حالياً (مسودة).');
    //     }
    //     $now = Carbon::now();
    //     if ($form->start_at && $now->lt(Carbon::parse($form->start_at))) {
    //         return abort(403, 'عذراً، التسجيل في هذا النشاط لم يبدأ بعد.');
    //     }
        
    //     if ($form->end_at && $now->gt(Carbon::parse($form->end_at))) {
    //         return abort(403, 'عذراً، تم إغلاق باب التسجيل في هذا النشاط لانتهاء الوقت المحدد.');
    //     }
    //     if($form->max_submissions && Submission::where('form_id' , $form->id)->count() >= $form->max_submissions){
    //         return abort(403, 'عذراً، انتهى عدد المسجلين.');
    //     }

    //     foreach($form->fields as $field){
    //         $value = $request->input("answers.{$field->id}");
    //         if($field->is_required && $field->field_type !== 'file' && empty($value)){
    //             return back()->withErrors(["answers.{$field->id}" => "الحقل ({$field->label}) مطلوب."])->withInput();
    //         }
    //         // file
    //         if ($field->field_type === 'file') {
    //             $hasFile = $request->hasFile("answers.{$field->id}");
                
    //             if ($field->is_required && !$hasFile) {
    //                 return back()->withErrors(["answers.{$field->id}" => "يجب رفع ملف للحقل ({$field->label})."])->withInput();
    //             }
    //         }

    //         // unique_check
    //         if ($field->unique_check && !empty($value)) {
    //             $exists = SubmissionValue::where('field_id', $field->id)
    //                 ->where('value', $value)
    //                 ->exists(); // تعيد true أو false بكفاءة عالية

    //             if ($exists) {
    //                 return back()->withErrors(["answers.{$field->id}" => "القيمة المدخلة في حقل ({$field->label}) مستخدمة من قبل."])->withInput();
    //             }
    //         }
    //     }
    //     $submission = Submission::create([
    //         'form_id' => $form->id,
    //         'ip_address' => $request->ip() ,
    //         'user_agent' => $request->header('User-Agent')
    //     ]); 

    //     foreach($form->fields as $field){
    //         $value = $request->input("answers.{$field->id}");
    //         if ($field->field_type === 'file' && $request->hasFile("answers.{$field->id}")) {
    //             $file = $request->file("answers.{$field->id}");
    //             // حفظ الملف في مجلد submissions داخل الـ storage
    //             $value = $file->store('submissions', 'public'); 
    //         }
    //         if (!empty($value)) {
    //             SubmissionValue::create([
    //                 'submission_id' => $submission->id,
    //                 'field_id' => $field->id,
    //                 'value' => $value
    //             ]);
    //         }
    //     }
    //     return redirect()->route('student.forms.success',['uuid' => $form->uuid]);
    // }

    public function submit(Request $request, $uuid)
    {
        $form = Form::where('uuid', $uuid)->with('fields')->firstOrFail();

        if ($form->status !== 'published') {
            return abort(403, 'عذراً، هذا النموذج غير متاح للتسجيل حالياً.');
        }

        $now = Carbon::now();
        if ($form->start_at && $now->lt(Carbon::parse($form->start_at))) {
            return abort(403, 'عذراً، التسجيل لم يبدأ بعد.');
        }
        if ($form->end_at && $now->gt(Carbon::parse($form->end_at))) {
            return abort(403, 'عذراً، تم إغلاق باب التسجيل لانتهاء الوقت.');
        }

        if ($form->max_submissions && Submission::where('form_id', $form->id)->count() >= $form->max_submissions) {
            return abort(403, 'عذراً، اكتمل العدد الأقصى للمسجلين في هذا النشاط.');
        }

        $answers = $request->input('answers', []);
        $fileAnswers = $request->file('answers', []);

        $validationRules = [];
        $customAttributes = [];

        foreach ($form->fields as $field) {
            $ruleKey = "answers.{$field->id}"; // صياغة المفتاح مثل: answers.1 أو answers.2
            $rules = [];

            if ($field->is_required) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }

            if ($field->field_type === 'email') {
                $rules[] = 'email';
            } elseif ($field->field_type === 'number') {
                $rules[] = 'numeric';
            } elseif ($field->field_type === 'file') {
                $rules[] = 'file|max:10240'; 
            }

            if ($field->unique_check && isset($answers[$field->id])) {
                $exists = SubmissionValue::where('field_id', $field->id)
                    ->where('value', $answers[$field->id])
                    ->exists();
                    
                if ($exists) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(["answers.{$field->id}" => "القيمة المدخلة في حقل ({$field->label}) مستخدمة من قبل!"]);
                }
            }

            if (!empty($rules)) {
                $validationRules[$ruleKey] = implode('|', $rules);
            }
            $customAttributes[$ruleKey] = $field->label;
        }

        $request->validate($validationRules, [], $customAttributes);



        DB::beginTransaction();

        
        try {
            $submission = Submission::create([
                'form_id'      => $form->id,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->header('User-Agent'),
            ]);

            foreach ($form->fields as $field) {
                $value = null;

                if ($field->field_type === 'file' && isset($fileAnswers[$field->id])) {
                        $file = $fileAnswers[$field->id];
                        $value = $file->store('submissions', 's3');
                } 
                else {
                    $value = $answers[$field->id] ?? null;
                }

                if ($value !== null && $value !== '') {
                    SubmissionValue::create([
                        'submission_id' => $submission->id,
                        'field_id'      => $field->id,
                        'value'         => $value,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('student.forms.success', $form->uuid);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['global' => 'حدث خطأ غير متوقع أثناء حفظ البيانات، يرجى المحاولة مرة أخرى.']);
        }
    }

    public function success($uuid)
    {
        $form = Form::where('uuid', $uuid)->firstOrFail();
        return view('student.forms.success', compact('form'));
    }
}