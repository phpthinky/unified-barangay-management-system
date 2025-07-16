@extends('layouts.app')
@section('title','Activity Logs')

@section('content')
<h3 class="mb-4">System Audit Logs</h3>

<table class="table table-hover">
    <thead class="table-light">
        <tr>
            <th>Date &amp; Time</th>
            <th>User</th>
            <th>Role</th>
            <th>Action</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>2025‑07‑15&nbsp;09:12</td>
            <td>Pedro Reyes</td>
            <td>Admin</td>
            <td>Approved Request</td>
            <td>Barangay Clearance #1023</td>
        </tr>
        <tr>
            <td>2025‑07‑15&nbsp;10:05</td>
            <td>Laura Ruiz</td>
            <td>Secretary</td>
            <td>Archived Official</td>
            <td>Outgoing Kagawad Ana Cruz</td>
        </tr>
        <tr>
            <td>2025‑07‑15&nbsp;11:43</td>
            <td>Juan Dela Cruz</td>
            <td>Resident</td>
            <td>Filed Complaint</td>
            <td>Noise disturbance (Complaint #215)</td>
        </tr>
    </tbody>
</table>

<div class="text-end">
    <button class="btn btn-outline-primary">Export Logs (CSV)</button>
</div>
@endsection
