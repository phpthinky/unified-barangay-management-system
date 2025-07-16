<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AbcDirectoryController extends Controller
{
    //
    public function index($value='')
    {
        // code...
        return view('abcdirectory.index');
    }
}
