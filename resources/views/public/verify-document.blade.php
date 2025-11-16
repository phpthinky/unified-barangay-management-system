{{-- resources/views/public/verify-document.blade.php --}}
@extends('layouts.public')

@section('title', 'Verify Document')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Verify Document</h1>

    <form method="GET" action="{{ route('public.verify-document') }}">
        <div class="mb-3">
            <label for="code" class="form-label">Enter Document Code</label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify</button>
    </form>

    @isset($valid)
        <div class="mt-5">
            @if($valid && $document)
                <div class="alert alert-success">
                    <h4>Valid Document</h4>
                    <p>Type: {{ ucfirst($type) }}</p>
                    <p>Reference #: {{ $document->reference_number }}</p>
                </div>
                <div class="mt-3">
                    {!! $qrCode ?? '' !!}
                </div>
            @else
                <div class="alert alert-danger">
                    Invalid or not found document.
                </div>
            @endif
        </div>
    @endisset
</div>
@endsection
