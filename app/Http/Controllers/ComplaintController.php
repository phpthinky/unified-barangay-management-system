<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function create() { return view('complaints.create'); }

    public function store(Request $r)
    {
        $r->validate([
            'category' => 'required|in:sanitation,noise,peace,others',
            'details'  => 'required|string|max:500',
        ]);

        Complaint::create([
            'user_id'  => auth()->id(),
            'category' => $r->category,
            'details'  => $r->details,
            'status'   => 'pending',
        ]);

        return redirect()->route('complaints.index')->with('success','Complaint sent!');
    }

    public function index()
    {
        $complaints = [];//Complaint::where('user_id', auth()->id())->latest()->get();
        return view('complaints.index', compact('complaints'));
    }
    public function lupon($value='')
    {
        // code...
        return view('complaints/lupon');
    }
}
