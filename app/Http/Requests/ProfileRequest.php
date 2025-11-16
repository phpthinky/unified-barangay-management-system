<?php
// app/Http/Requests/ProfileRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'mother_maiden_name' => 'nullable|string|max:100',
            'birthdate' => 'required|date|before:-18 years',
            'gender' => 'required|in:male,female,other',
            'contact_number' => 'required|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/',
            'email' => 'nullable|email|max:100',
            'house_number' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:100',
            'purok' => 'required|string|max:50', 
            'barangay_id' => 'required|exists:barangays,id',
            'municipality' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'zipcode' => 'nullable|string|max:10',
            'valid_id_type' => 'required|string|max:50',
            'valid_id_number' => 'required|string|max:50',
            'valid_id_path' => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'proof_of_residency_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'occupation' => 'nullable|string|max:100',
            'civil_status' => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:50'
        ];
    }

    public function messages()
    {
        return [
            'birthdate.before' => 'You must be at least 18 years old to register.',
            'valid_id_path.required' => 'Please upload a valid ID for verification.',
            'contact_number.regex' => 'Please enter a valid phone number.'
        ];
    }
}