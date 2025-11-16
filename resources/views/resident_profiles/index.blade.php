@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Resident Profiles</h2>
    <a href="{{ route('resident_profiles.create') }}" class="btn btn-success mb-3">Add Profile</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Barangay</th>
                <th>Contact</th>
                <th>Valid ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($profiles as $profile)
            <tr>
                <td>{{ $profile->first_name }} {{ $profile->middle_name }} {{ $profile->last_name }}</td>
                <td>{{ $profile->barangay }}</td>
                <td>{{ $profile->contact_number }}</td>
                <td>
                    @if($profile->valid_id_path)
                        <a href="{{ asset('storage/'.$profile->valid_id_path) }}" target="_blank">View</a>
                    @endif
                </td>
                <td>
                    <a href="{{ route('resident_profiles.edit', $profile->id) }}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="{{ route('resident_profiles.destroy', $profile->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $profiles->links() }}
</div>
@endsection
