@extends('layouts.app')
@section('title','Walk‑In Assistance')

@section('content')
<div class="text-center p-5 bg-white shadow-sm">
    <h2 class="mb-3">Need Help Without Internet?</h2>
    <p class="lead">
        Residents who do not have access to a smartphone, computer, or stable data
        connection can still obtain barangay services in person.
    </p>
    <ol class="list-group list-group-numbered text-start mx-auto" style="max-width:600px">
        <li class="list-group-item">Visit the Barangay Hall during office hours (8 AM – 5 PM).</li>
        <li class="list-group-item">Proceed to the <strong>Help Desk</strong> for manual forms.</li>
        <li class="list-group-item">Bring a valid ID for verification.</li>
        <li class="list-group-item">For special assistance, ask for the <em>ICT Focal Person</em>.</li>
    </ol>
    <div class="alert alert-info mt-4">
        <strong>Tip:</strong> You may register online later with the help of barangay staff.
    </div>
</div>
@endsection
