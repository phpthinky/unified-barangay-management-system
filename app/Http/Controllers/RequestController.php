<?php

// app/Http/Controllers/RequestController.php
namespace App\Http\Controllers;

//use App\Models\RequestDoc;
use Illuminate\Http\Request as HttpRequest;

class RequestController extends Controller
{
    public function index()
        {
            $requests = [];// RequestDoc::where('user_id', auth()->id())->latest()->get();
            return view('requests.index', compact('requests'));
        }

    public function create()   { return view('requests.create'); }

    public function store(HttpRequest $r)
    {
        $r->validate([
            'type'   => 'required|in:clearance,indigency,permit',
            'purpose'=> 'required|string|max:255',
        ]);

        RequestDoc::create([
            'user_id' => auth()->id(),
            'type'    => $r->type,
            'purpose' => $r->purpose,
            'status'  => 'pending',
        ]);

        return redirect()->route('requests.index')
                         ->with('success','Request submitted!');
    }
}
