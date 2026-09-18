<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// مسارات الضيوف (تسجيل الدخول)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store']);
    
});

// Route::resource('forms', FormController::class)->middleware('auth'); ===
// مسارات لوحة التحكم والإدارة (محمية بالكامل)
// Route::get('/forms', [FormController::class, 'index'])->name('forms.index')->middleware('auth');
// Route::get('/forms/create', [FormController::class, 'create'])->name('forms.create')->middleware('auth');
// Route::post('/forms', [FormController::class, 'store'])->name('forms.store')->middleware('auth');
// Route::get('/forms/{form}', [FormController::class, 'show'])->name('forms.show')->middleware('auth');
// Route::get('/forms/{form}/edit', [FormController::class, 'edit'])->name('forms.edit')->middleware('auth');
// Route::put('/forms/{form}', [FormController::class, 'update'])->name('forms.update')->middleware('auth');
// Route::delete('/forms/{form}', [FormController::class, 'destroy'])->name('forms.destroy')->middleware('auth');


// مسارات لوحة التحكم والإدارة (محمية بالكامل)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class , 'index'])->name('dashboard');
    Route::resource('forms', FormController::class);
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/forms/{form}/submissions', [SubmissionController::class, 'index'])->name('forms.submissions');
    Route::get('/forms/{form}/submissions/export', [SubmissionController::class, 'export'])->name('forms.submissions.export');
    Route::get('/submissions/files/{submissionValue}', [SubmissionController::class, 'viewFile'])
    ->name('forms.submissions.file');
});

use App\Http\Controllers\Student\StudentFormController;

// مسارات بوابة الطلاب العامة (بدون تسجيل دخول)
Route::prefix('s')->group(function () {
    
    // عرض النموذج ديناميكياً للطالب بناءً على الـ UUID
    Route::get('/forms/{uuid}', [StudentFormController::class, 'show'])->name('student.forms.show');
    
    // استقبال وحفظ رد الطالب في قاعدة البيانات
    Route::post('/forms/{uuid}/submit', [StudentFormController::class, 'submit'])->name('student.forms.submit');
    
    // صفحة نجاح التقديم
    Route::get('/forms/{uuid}/success', [StudentFormController::class, 'success'])->name('student.forms.success');
});