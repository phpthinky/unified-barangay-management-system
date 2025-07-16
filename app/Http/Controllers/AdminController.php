<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function index($value='')
    {
        // code...
    }

    public function dashboard($value='')
    {
        // code...
        return view('admin.dashboard');
    }
    public function archive($value='')
    {
        // code...
        return view('admin.archive');
    }
}
