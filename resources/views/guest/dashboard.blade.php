@extends('layouts.public')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        {{-- Welcome Header --}}
        <div class="text-center mb-12">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/sablayan-logo.png') }}" alt="Sablayan Logo" class="h-24 w-24">
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                Welcome to UBMS
            </h1>
            <p class="text-xl text-gray-600">
                Unified Barangay Management System
            </p>
            <p class="text-lg text-gray-500 mt-2">
                Municipality of Sablayan, Occidental Mindoro
            </p>
        </div>

        {{-- User Info Card --}}
        @auth
        <div class="bg-white rounded-lg shadow-md p-6 mb-8 max-w-2xl mx-auto">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-semibold text-gray-900">
                        Hello, {{ auth()->user()->first_name }}!
                    </h2>
                    <p class="text-gray-600">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->barangay)
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Barangay {{ auth()->user()->barangay->name }}
                            </span>
                        </p>
                    @endif
                </div>
            </div>
        </div>
        @endauth

        {{-- Status Message --}}
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 max-w-2xl mx-auto">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-yellow-800 mb-2">
                        Account Verification in Progress
                    </h3>
                    <div class="text-sm text-yellow-700 space-y-1">
                        @auth
                            @if(!auth()->user()->is_verified)
                                <p>✓ Email verified</p>
                                <p>⏱ <strong>Awaiting barangay staff verification</strong></p>
                                <p class="mt-3">Your account is currently being reviewed by the barangay staff. Once verified, you'll be able to:</p>
                            @else
                                <p>✓ Account verified</p>
                                <p>⏱ <strong>Checking system role assignment</strong></p>
                                <p class="mt-3">Your account is verified but may not have been assigned a system role yet. Please contact your barangay office.</p>
                            @endif
                        @else
                            <p>You need to register and verify your account to access the system.</p>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Services Grid --}}
        <div class="max-w-4xl mx-auto mb-8">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6 text-center">
                Available Services (After Verification)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                {{-- Document Requests --}}
                <div class="bg-white rounded-lg shadow-md p-6 opacity-60">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mb-4 mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">Document Requests</h4>
                    <p class="text-sm text-gray-600 text-center">Request barangay clearance, certificates, and other documents online</p>
                </div>

                {{-- File Complaints --}}
                <div class="bg-white rounded-lg shadow-md p-6 opacity-60">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-500 text-white mb-4 mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">File Complaints</h4>
                    <p class="text-sm text-gray-600 text-center">Submit complaints following Katarungang Pambarangay procedures</p>
                </div>

                {{-- Track Requests --}}
                <div class="bg-white rounded-lg shadow-md p-6 opacity-60">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-green-500 text-white mb-4 mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">Track Status</h4>
                    <p class="text-sm text-gray-600 text-center">Monitor your document requests and complaints in real-time</p>
                </div>

                {{-- Profile Management --}}
                <div class="bg-white rounded-lg shadow-md p-6 opacity-60">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white mb-4 mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">Profile Management</h4>
                    <p class="text-sm text-gray-600 text-center">Update your information and manage your account settings</p>
                </div>

                {{-- Notifications --}}
                <div class="bg-white rounded-lg shadow-md p-6 opacity-60">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-500 text-white mb-4 mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">Notifications</h4>
                    <p class="text-sm text-gray-600 text-center">Receive updates on your requests via email and SMS</p>
                </div>

                {{-- QR Verification --}}
                <div class="bg-white rounded-lg shadow-md p-6 opacity-60">
                    <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4 mx-auto">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 text-center mb-2">QR Code Documents</h4>
                    <p class="text-sm text-gray-600 text-center">All documents include QR codes for instant verification</p>
                </div>

            </div>
        </div>

        {{-- What to Do Next --}}
        <div class="bg-white rounded-lg shadow-md p-8 max-w-3xl mx-auto mb-8">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6 text-center">
                What to Do Next
            </h3>
            
            @auth
                @if(!auth()->user()->is_verified)
                    {{-- Pending Verification --}}
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Step 1: Account Created ✓</h4>
                                <p class="text-gray-600">You've successfully registered and verified your email.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-yellow-100 text-yellow-600">
                                    <svg class="h-5 w-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Step 2: Barangay Verification (In Progress)</h4>
                                <p class="text-gray-600">Barangay staff is reviewing your information. This typically takes 1-2 business days.</p>
                            </div>
                        </div>

                        <div class="flex items-start opacity-50">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-gray-100 text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-medium text-gray-900">Step 3: Full Access</h4>
                                <p class="text-gray-600">Once verified, you'll receive an email and can access all services.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-3">Need help with verification?</h4>
                        <p class="text-gray-600 mb-4">
                            If it's been more than 2 business days or you need urgent access, please visit your barangay office.
                        </p>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-700">
                                <strong>Barangay Office</strong><br>
                                @if(auth()->user()->barangay)
                                    Barangay {{ auth()->user()->barangay->name }}<br>
                                    Contact: {{ auth()->user()->barangay->contact_number ?? 'N/A' }}<br>
                                    Email: {{ auth()->user()->barangay->email ?? 'N/A' }}
                                @else
                                    Contact your barangay office for assistance
                                @endif
                            </p>
                        </div>
                    </div>
                @else
                    {{-- Verified but no role --}}
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">
                            Your account is verified, but you may not have been assigned a proper system role yet.
                        </p>
                        <p class="text-gray-600 mb-6">
                            Please contact your barangay office to have your account properly configured.
                        </p>
                        <div class="bg-gray-50 rounded-lg p-6 inline-block">
                            <p class="text-sm text-gray-700">
                                <strong>Barangay Office</strong><br>
                                @if(auth()->user()->barangay)
                                    Barangay {{ auth()->user()->barangay->name }}<br>
                                    Contact: {{ auth()->user()->barangay->contact_number ?? 'N/A' }}<br>
                                    Email: {{ auth()->user()->barangay->email ?? 'N/A' }}
                                @else
                                    Contact your barangay office for assistance
                                @endif
                            </p>
                        </div>
                    </div>
                @endif
            @else
                {{-- Not logged in --}}
                <div class="text-center space-y-6">
                    <p class="text-gray-600 text-lg">
                        Get started with UBMS by creating your account
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Create Account
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 border border-indigo-600 text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50">
                            Login
                        </a>
                    </div>
                </div>
            @endauth
        </div>

        {{-- FAQ Section --}}
        <div class="bg-white rounded-lg shadow-md p-8 max-w-3xl mx-auto mb-8">
            <h3 class="text-2xl font-semibold text-gray-900 mb-6 text-center">
                Frequently Asked Questions
            </h3>
            <div class="space-y-4">
                <details class="group">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <span>How long does verification take?</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                        </span>
                    </summary>
                    <p class="text-gray-600 mt-3 px-4 pb-4">
                        Barangay staff typically verifies accounts within 1-2 business days. You'll receive an email notification once your account is verified.
                    </p>
                </details>

                <details class="group">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <span>What do I need to be verified?</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                        </span>
                    </summary>
                    <p class="text-gray-600 mt-3 px-4 pb-4">
                        You need to be a verified resident of your barangay and have lived there for at least 6 months. Staff will check your information against the Registry of Barangay Inhabitants (RBI).
                    </p>
                </details>

                <details class="group">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <span>Can I request documents while waiting?</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                        </span>
                    </summary>
                    <p class="text-gray-600 mt-3 px-4 pb-4">
                        No, you must wait for account verification before you can request documents or file complaints. However, you can visit the barangay office in person for urgent needs.
                    </p>
                </details>

                <details class="group">
                    <summary class="flex justify-between items-center font-medium cursor-pointer list-none p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <span>What if I'm a new resident?</span>
                        <span class="transition group-open:rotate-180">
                            <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24"><path d="M6 9l6 6 6-6"></path></svg>
                        </span>
                    </summary>
                    <p class="text-gray-600 mt-3 px-4 pb-4">
                        If you've lived in the barangay for less than 6 months, you won't be able to request documents online yet. You'll need to wait until you meet the 6-month residency requirement. Visit your barangay office for assistance in the meantime.
                    </p>
                </details>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="text-center">
            @auth
                <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    View My Profile
                </a>
                <span class="text-gray-400 mx-4">|</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-800 font-medium">
                        Logout
                    </button>
                </form>
            @else
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Login here</a>
                </p>
            @endauth
        </div>

    </div>
</div>
@endsection