<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Form;
use App\Models\Submission;
use App\Models\SubmissionValue;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. جلب النموذج رقم 5 مع حقوله (تأكد أن الرقم 5 موجود في قاعدة بياناتك أو استبدله برقم نموذج موجود)
        $form = Form::with('fields')->find(5);

        if (!$form) {
            $this->command->error('النموذج رقم 5 غير موجود! يرجى التأكد من رقم المعرف (ID).');
            return;
        }

        $this->command->info("بدء توليد 50 رداً عشوائياً للنموذج: {$form->title}");

        // 2. حلقة تكرار لتوليد 50 رداً
        for ($i = 1; $i <= 50; $i++) {
            
            // إنشاء السجل الرئيسي في جدول submissions
            $submission = Submission::create([
                'form_id'    => $form->id,
                'ip_address' => fake()->ipv4(), // توليد عنوان IP عشوائي
                'user_agent' => fake()->userAgent(), // توليد بيانات متصفح عشوائية
                'created_at' => fake()->dateTimeBetween('-1 month', 'now'), // تاريخ عشوائي خلال الشهر الماضي
            ]);

            // 3. لكل رد، نمر على حقول النموذج لنصنع لها إجابة عشوائية في جدول submission_values
            foreach ($form->fields as $field) {
                
                // توليد إجابة ذكية بناءً على نوع الحقل
                $randomValue = match ($field->field_type) {
                    'email' => fake()->safeEmail(),
                    'number'=> fake()->numberBetween(10, 100),
                    'file'  => 'submissions/fake_file_' . fake()->word() . '.pdf',
                    default => fake()->name(), // إذا كان نص عادي نضع اسماً عشوائياً
                };

                // حفظ القيمة في جدول submission_values
                SubmissionValue::create([
                    'submission_id' => $submission->id,
                    'field_id'      => $field->id,
                    'value'         => $randomValue,
                ]);
            }
        }

        $this->command->info('تم توليد 50 رداً عشوائياً بنجاح! 🎉');
    }
}
