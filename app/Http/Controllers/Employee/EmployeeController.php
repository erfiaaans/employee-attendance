<?php

namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //
    public function employeeGate()
    {
        return view('employee.index');
    }
}
