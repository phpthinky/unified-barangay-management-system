<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
public function index($value='')
{
    // code...
    return view('reports/index');
}
}
