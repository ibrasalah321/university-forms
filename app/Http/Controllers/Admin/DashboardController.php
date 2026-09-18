<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Submission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // جلب إحصائيات سريعة لعرضها في الـ Dashboard
        $totalForms = Form::count();
        $activeForms = Form::where('status', 'published')->count();
        $totalSubmissions = Submission::count();

        return view('admin.dashboard' , compact('totalForms', 'activeForms', 'totalSubmissions'));
    }
}