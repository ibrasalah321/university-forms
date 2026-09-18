<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Submission;
use App\Models\SubmissionValue;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index($formId){
        
        $form = Form::with('fields')->findOrFail($formId);
        $submissions = $form->submissions()->with('values')->paginate(10);
        $submissionsCount = $form->submissions()->with('values')->count();
        

        return view('admin.submissions.index', compact('form', 'submissions', 'submissionsCount'));
    }
    
    public function export($formId)
    {
        $form = Form::with('fields')->findOrFail($formId); 
        $submissions = $form->submissions()->with('values')->get(); 

        return response()->stream(function () use ($form, $submissions) { 

            $stream = fopen('php://output', 'w'); 

            // إضافة BOM لتجنب مشاكل الترميز العربي في Excel
            fprintf($stream, chr(0xEF).chr(0xBB).chr(0xBF)); 

            // رؤوس الأعمدة
            $headers = [
                'Submission ID',
                'IP Address',
                'User Agent',
                'Submitted At'
            ]; 

            foreach ($form->fields as $field) { 
                $headers[] = $field->label; 
            } 

            fputcsv($stream, $headers); 

            // البيانات
            foreach ($submissions as $submission) { 

                $row = [ 
                    $submission->id, 
                    $submission->ip_address, 
                    $submission->user_agent, 
                    $submission->created_at->format('Y-m-d H:i:s'), 
                ]; 

                foreach ($form->fields as $field) { 

                    $value = $submission->values
                        ->where('field_id', $field->id)
                        ->first();

                    if ($field->field_type === 'file') { 

                        $row[] = $value
                            ? route('forms.submissions.file', $value->id)
                            : '';

                    } else { 

                        $row[] = $value
                            ? $value->value
                            : '';
                    }
                } 

                fputcsv($stream, $row); 
            } 

            fclose($stream); 

        }, 200, [ 
            'Content-Type'        => 'text/csv; charset=UTF-8', 
            'Content-Disposition' => 'attachment; filename="' . $form->title . '_ردود.csv"', 
            'Pragma'              => 'no-cache', 
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0', 
            'Expires'             => '0',
        ]); 
    }
    public function viewFile(SubmissionValue $submissionValue)
    {
        $field = $submissionValue->field;

        if (!$field || $field->field_type !== 'file') {
            abort(404);
        }

        $path = $submissionValue->value;

        if (!Storage::disk('s3')->exists($path)) {
            abort(404, 'الملف غير موجود.');
        }

        $mimeType = Storage::disk('s3')->mimeType($path);

        return response(
            Storage::disk('s3')->get($path),
            200,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline',
            ]
        );
    }
}
