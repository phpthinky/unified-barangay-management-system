@csrf
<div class="row">
    <div class="col-md-4">
        <label>First Name</label>
        <input type="text" name="first_name" value="{{ old('first_name', $residentProfile->first_name ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label>Middle Name</label>
        <input type="text" name="middle_name" value="{{ old('middle_name', $residentProfile->middle_name ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Last Name</label>
        <input type="text" name="last_name" value="{{ old('last_name', $residentProfile->last_name ?? '') }}" class="form-control" required>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <label>Mother's Maiden Name</label>
        <input type="text" name="mother_maiden_name" value="{{ old('mother_maiden_name', $residentProfile->mother_maiden_name ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Birthdate</label>
        <input type="date" name="birthdate" value="{{ old('birthdate', $residentProfile->birthdate ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Gender</label>
        <input type="text" name="gender" value="{{ old('gender', $residentProfile->gender ?? '') }}" class="form-control">
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <label>Contact Number</label>
        <input type="text" name="contact_number" value="{{ old('contact_number', $residentProfile->contact_number ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $residentProfile->email ?? '') }}" class="form-control">
    </div>
</div>

<hr>

<div class="row mt-3">
    <div class="col-md-3">
        <label>House Number</label>
        <input type="text" name="house_number" value="{{ old('house_number', $residentProfile->house_number ?? '') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Street</label>
        <input type="text" name="street" value="{{ old('street', $residentProfile->street ?? '') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Purok</label>
        <input type="text" name="purok" value="{{ old('purok', $residentProfile->purok ?? '') }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label>Barangay</label>
        <input type="text" name="barangay" value="{{ old('barangay', $residentProfile->barangay ?? '') }}" class="form-control" required>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <label>Municipality</label>
        <input type="text" name="municipality" value="{{ old('municipality', $residentProfile->municipality ?? 'Sablayan') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Province</label>
        <input type="text" name="province" value="{{ old('province', $residentProfile->province ?? 'Occidental Mindoro') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Zipcode</label>
        <input type="text" name="zipcode" value="{{ old('zipcode', $residentProfile->zipcode ?? '') }}" class="form-control">
    </div>
</div>

<hr>

<div class="row mt-3">
    <div class="col-md-4">
        <label>Valid ID Type</label>
        <input type="text" name="valid_id_type" value="{{ old('valid_id_type', $residentProfile->valid_id_type ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Valid ID Number</label>
        <input type="text" name="valid_id_number" value="{{ old('valid_id_number', $residentProfile->valid_id_number ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Valid ID File</label>
        <input type="file" name="valid_id_path" class="form-control">
        @if(isset($residentProfile) && $residentProfile->valid_id_path)
            <small>Current: <a href="{{ asset('storage/'.$residentProfile->valid_id_path) }}" target="_blank">View</a></small>
        @endif
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <label>Proof of Residency File</label>
        <input type="file" name="proof_of_residency_path" class="form-control">
        @if(isset($residentProfile) && $residentProfile->proof_of_residency_path)
            <small>Current: <a href="{{ asset('storage/'.$residentProfile->proof_of_residency_path) }}" target="_blank">View</a></small>
        @endif
    </div>
    <div class="col-md-4">
        <label>Occupation</label>
        <input type="text" name="occupation" value="{{ old('occupation', $residentProfile->occupation ?? '') }}" class="form-control">
    </div>
    <div class="col-md-4">
        <label>Civil Status</label>
        <input type="text" name="civil_status" value="{{ old('civil_status', $residentProfile->civil_status ?? '') }}" class="form-control">
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4">
        <label>Nationality</label>
        <input type="text" name="nationality" value="{{ old('nationality', $residentProfile->nationality ?? 'Filipino') }}" class="form-control">
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">Save</button>
</div>
