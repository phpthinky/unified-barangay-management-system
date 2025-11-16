{{-- resources/views/public/barangay/officials.blade.php --}}
@extends('layouts.public')

@section('title', $barangay->name . ' Officials')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Barangay Officials of {{ $barangay->name }}</h1>

    <div class="row">
        @if($captain)
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Barangay Captain</h5>
                        <p>{{ $captain->name }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($secretary)
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h5>Barangay Secretary</h5>
                        <p>{{ $secretary->name }}</p>
                    </div>
                </div>
            </div>
        @endif

        @foreach($staff as $member)
            <div class="col-md-3 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h6>{{ $member->role }}</h6>
                        <p>{{ $member->name }}</p>
                    </div>
                </div>
            </div>
        @endforeach

        @if($luponMembers && count($luponMembers))
            <div class="col-12 mt-4">
                <h4>Lupon Members</h4>
                <ul>
                    @foreach($luponMembers as $lupon)
                        <li>{{ $lupon->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
