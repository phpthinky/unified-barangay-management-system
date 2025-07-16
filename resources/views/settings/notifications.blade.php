{{-- resources/views/settings/notifications.blade.php --}}
@extends('layouts.app')
@section('title','Notification Settings')

@section('content')
<h3 class="mb-4">Notification Settings</h3>

<div class="card col-md-6 shadow-sm p-4">
  <form>
    <div class="mb-3">
      <label class="form-label">Email Sender Address</label>
      <input type="email" class="form-control" value="noreply@barangay.gov.ph">
    </div>

    <div class="mb-3">
      <label class="form-label">SMS API Key</label>
      <input class="form-control" value="••••••••••••">
    </div>

    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" id="emailToggle" checked>
      <label class="form-check-label" for="emailToggle">Enable Email Notifications</label>
    </div>

    <div class="form-check form-switch mb-4">
      <input class="form-check-input" type="checkbox" id="smsToggle" checked>
      <label class="form-check-label" for="smsToggle">Enable SMS Notifications</label>
    </div>

    <button class="btn btn-primary">Save Settings</button>
  </form>
</div>
@endsection
