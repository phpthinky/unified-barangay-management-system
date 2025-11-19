<?php

use Illuminate\Support\Facades\Route;

// Public Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Guest\EmailVerificationController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ResidentController as AdminResidentController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\ComplaintTypeController;
use App\Http\Controllers\Admin\BusinessPermitTypeController;
use App\Http\Controllers\Admin\ActiveSessionsController;
use App\Http\Controllers\Admin\DatabaseResetController;

// ABC Controllers
use App\Http\Controllers\Abc\DashboardController as AbcDashboardController;
use App\Http\Controllers\Abc\ReportController as AbcReportController;
use App\Http\Controllers\Abc\UserController as AbcUserController;
use App\Http\Controllers\Abc\BarangayController as AbcBarangayController;

// Barangay Controllers
use App\Http\Controllers\Barangay\DashboardController as BarangayDashboardController;
use App\Http\Controllers\Barangay\ResidentController as BarangayResidentController;
use App\Http\Controllers\Barangay\DocumentRequestController as BarangayDocumentController;
use App\Http\Controllers\Barangay\ComplaintController as BarangayComplaintController;
use App\Http\Controllers\Barangay\ComplaintWorkflowController;
use App\Http\Controllers\Barangay\BusinessPermitController as BarangayPermitController;
use App\Http\Controllers\Barangay\ReportController as BarangayReportController;
use App\Http\Controllers\Barangay\LuponController as BarangayLuponController;
use App\Http\Controllers\Barangay\InhabitantController as BarangayInhabitantController;
use App\Http\Controllers\Barangay\BarangayUserController;
use App\Http\Controllers\Barangay\RBISearchController;

// Lupon Controllers
use App\Http\Controllers\Lupon\DashboardController as LuponDashboardController;
use App\Http\Controllers\Lupon\ComplaintController as LuponComplaintController;
use App\Http\Controllers\Lupon\HearingController as LuponHearingController;

// Resident Controllers
use App\Http\Controllers\Resident\DashboardController as ResidentDashboardController;
use App\Http\Controllers\Resident\ProfileController as ResidentProfileController;
use App\Http\Controllers\Resident\DocumentRequestController as ResidentDocumentController;
use App\Http\Controllers\Resident\ComplaintController as ResidentComplaintController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('public.about');
Route::get('/barangays', [PublicController::class, 'barangays'])->name('public.barangays');
Route::get('/services', [PublicController::class, 'services'])->name('public.services');
Route::get('/demo', [PublicController::class, 'demoAccount'])->name('public.demo');

// Document/Request Tracking
Route::get('/track/{tracking_number}', [PublicController::class, 'trackRequest'])->name('track.request');
Route::get('/verify/{qr_code}', [PublicController::class, 'verifyDocument'])->name('verify.document');

Route::get('/guest-dashboard', function () {
    return view('guest.dashboard');
})->name('guest.dashboard')->middleware('auth');
/*
|--------------------------------------------------------------------------
| Barangay Public Routes (via /b/{slug})
|--------------------------------------------------------------------------
*/

Route::prefix('b/{barangay:slug}')->name('public.barangay.')->group(function () {
    Route::get('/', [PublicController::class, 'barangayHome'])->name('home');
    Route::get('/services', [PublicController::class, 'barangayServices'])->name('services');
    Route::get('/officials', [PublicController::class, 'barangayOfficials'])->name('officials');
    Route::get('/contact', [PublicController::class, 'barangayContact'])->name('contact');
    
    // Multi-step Registration
    Route::get('/register', [PublicController::class, 'barangayRegister'])->name('register');
    Route::post('/register/continue', [PublicController::class, 'registerContinue'])->name('register.continue');
    Route::get('/register/rbi-check', [PublicController::class, 'registerRbiCheck'])->name('register.rbi-check');
    Route::post('/register/check-rbi', [PublicController::class, 'registerCheckRbi'])->name('register.check-rbi');
    Route::get('/register/password', [PublicController::class, 'registerPassword'])->name('register.password');
    Route::post('/register/complete-rbi', [PublicController::class, 'registerCompleteRbi'])->name('register.complete-rbi');
    Route::get('/register/not-found', [PublicController::class, 'registerNotFound'])->name('register.not-found');
    Route::get('/register/full-form', [PublicController::class, 'registerFullForm'])->name('register.full-form');
    Route::post('/register/complete-full', [PublicController::class, 'registerCompleteFull'])->name('register.complete-full');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Password Reset
Route::prefix('password')->name('password.')->group(function () {
    Route::get('reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('request');
    Route::post('email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('email');
    Route::get('reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('reset');
    Route::post('reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('update');
});

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('email')->name('verification.')->group(function () {
    Route::get('verify', [EmailVerificationController::class, 'notice'])->name('notice');
    Route::post('verify', [EmailVerificationController::class, 'verify'])->name('verify');
    Route::post('resend', [EmailVerificationController::class, 'resend'])->name('resend');
    Route::get('verified', [EmailVerificationController::class, 'success'])->name('success');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    // Dashboard Redirect
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    // Profile Routes (Common for all users)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [HomeController::class, 'profile'])->name('show');
        Route::put('/', [HomeController::class, 'updateProfile'])->name('update');
        Route::post('photo', [HomeController::class, 'updatePhoto'])->name('photo');
        Route::post('password', [HomeController::class, 'changePassword'])->name('password');
    });

    /*
    |--------------------------------------------------------------------------
    | Municipality Admin Routes
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('admin')->name('admin.')->middleware('ubms.admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Resident Management
        Route::prefix('residents')->name('residents.')->group(function () {
            Route::get('/', [AdminResidentController::class, 'index'])->name('index');
            Route::get('/{resident}', [AdminResidentController::class, 'show'])->name('show');
            Route::post('/{resident}/verify', [AdminResidentController::class, 'verify'])->name('verify');
            Route::delete('/{resident}/unverify', [AdminResidentController::class, 'unverify'])->name('unverify');
            Route::get('/export/excel', [AdminResidentController::class, 'exportExcel'])->name('export.excel');
        });

    });

    /*
    |--------------------------------------------------------------------------
    | ABC President Routes
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('abc')->name('abc.')->middleware('ubms.abc')->group(function () {
        Route::get('/dashboard', [AbcDashboardController::class, 'index'])->name('dashboard');
        
        // ABC Profile
        Route::prefix('profile')->group(function () {
            Route::get('/', [AbcDashboardController::class, 'profile'])->name('profile');
            Route::put('/', [AbcDashboardController::class, 'updateProfile'])->name('profile.update');
            Route::post('photo', [AbcDashboardController::class, 'updatePhoto'])->name('profile.photo');
            Route::post('password', [AbcDashboardController::class, 'changePassword'])->name('profile.password');
        });
        
        // Active Sessions Management
        Route::prefix('active-sessions')->name('active-sessions.')->group(function () {
            Route::get('/', [ActiveSessionsController::class, 'index'])->name('index');
            Route::delete('/{user}/force-logout', [ActiveSessionsController::class, 'forceLogout'])->name('force-logout');
            Route::post('/force-logout-multiple', [ActiveSessionsController::class, 'forceLogoutMultiple'])->name('force-logout-multiple');
            Route::post('/clear-inactive', [ActiveSessionsController::class, 'clearInactive'])->name('clear-inactive');
        });
        
        // Database Reset (ABC President Only)
        Route::prefix('database')->name('database.')->group(function () {
            Route::get('/reset', [DatabaseResetController::class, 'showResetPage'])->name('reset.show');
            Route::post('/reset', [DatabaseResetController::class, 'reset'])->name('reset.execute');
        });

        // Barangay Management
        Route::resource('barangays', AbcBarangayController::class);
        Route::post('barangays/{barangay}/generate-qr', [AbcBarangayController::class, 'generateQr'])->name('barangays.generate-qr');
        Route::get('barangays/{barangay}/qr-inline', [AbcBarangayController::class, 'getQrCodeInline'])->name('barangays.qr-inline');

        // Barangay Officials Management (Organizational Chart)
        Route::resource('barangay-officials', \App\Http\Controllers\Admin\BarangayOfficialController::class)->names('barangay-officials');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AbcUserController::class, 'index'])->name('index');
            Route::get('/create', [AbcUserController::class, 'create'])->name('create');
            Route::post('/', [AbcUserController::class, 'store'])->name('store');
            Route::get('/{user}', [AbcUserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AbcUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AbcUserController::class, 'update'])->name('update');
            Route::delete('/{user}/archive', [AbcUserController::class, 'archive'])->name('archive');
            Route::post('/{user}/restore', [AbcUserController::class, 'restore'])->name('restore');
            Route::post('/{user}/toggle-status', [AbcUserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-archive', [AbcUserController::class, 'bulkArchive'])->name('bulk-archive');
            Route::get('/export/excel', [AbcUserController::class, 'exportExcel'])->name('export-excel');
        });

        // Executive Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [AbcReportController::class, 'index'])->name('index');
            Route::get('/summary', [AbcReportController::class, 'summary'])->name('summary');
            Route::get('/barangay/{barangay}', [AbcReportController::class, 'barangayReport'])->name('barangay');
            Route::post('/export', [AbcReportController::class, 'export'])->name('export');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Barangay Staff Routes
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('barangay')->name('barangay.')->middleware('ubms.barangay')->group(function () {
        Route::get('/dashboard', [BarangayDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [BarangayDashboardController::class, 'profile'])->name('profile');
        
        // RBI Search
        Route::get('/rbi/search', [RBISearchController::class, 'search'])->name('rbi.search');
        
        // Barangay Inhabitants Registry (RBI)
        Route::prefix('inhabitants')->name('inhabitants.')->group(function () {
            Route::get('/', [BarangayInhabitantController::class, 'index'])->name('index');
            Route::get('/create', [BarangayInhabitantController::class, 'create'])->name('create');
            Route::post('/', [BarangayInhabitantController::class, 'store'])->name('store');
            Route::get('/{inhabitant}', [BarangayInhabitantController::class, 'show'])->name('show');
            Route::get('/{inhabitant}/edit', [BarangayInhabitantController::class, 'edit'])->name('edit');
            Route::put('/{inhabitant}', [BarangayInhabitantController::class, 'update'])->name('update');
            Route::post('/{inhabitant}/verify', [BarangayInhabitantController::class, 'verify'])->name('verify');
            Route::post('/{inhabitant}/unverify', [BarangayInhabitantController::class, 'unverify'])->name('unverify');
            Route::delete('/{inhabitant}', [BarangayInhabitantController::class, 'destroy'])->name('destroy');
            Route::get('/quick-create/{residentId}', [BarangayInhabitantController::class, 'quickCreate'])->name('quick-create');
            Route::post('/quick-store/{residentId}', [BarangayInhabitantController::class, 'quickStore'])->name('quick-store');
        });

        // Resident Management
        Route::prefix('residents')->name('residents.')->group(function () {
            Route::get('/', [BarangayResidentController::class, 'index'])->name('index');
            Route::get('/pending', [BarangayResidentController::class, 'pending'])->name('pending');
            Route::get('/{resident}', [BarangayResidentController::class, 'show'])->name('show');
            Route::post('/{resident}/verify', [BarangayResidentController::class, 'verify'])->name('verify');
            Route::delete('/{resident}/unverify', [BarangayResidentController::class, 'unverify'])->name('unverify');
            Route::post('/{resident}/reverify', [BarangayResidentController::class, 'reverify'])->name('reverify');
            Route::post('/{resident}/link-rbi', [BarangayResidentController::class, 'linkRbi'])->name('link-rbi');
            Route::get('/export/excel', [BarangayResidentController::class, 'exportExcel'])->name('export.excel');
        });
        
        // Document Requests
        Route::prefix('documents')->name('documents.')->group(function () {
            Route::get('/', [BarangayDocumentController::class, 'index'])->name('index');
            Route::get('/{documentRequest}', [BarangayDocumentController::class, 'show'])->name('show');
            Route::put('/{documentRequest}/process', [BarangayDocumentController::class, 'process'])->name('process');
            Route::put('/{documentRequest}/approve', [BarangayDocumentController::class, 'approve'])->name('approve');
            Route::put('/{documentRequest}/reject', [BarangayDocumentController::class, 'reject'])->name('reject');
            Route::get('/{documentRequest}/generate-pdf', [BarangayDocumentController::class, 'generatePdf'])->name('generate-pdf');
            Route::get('/{documentRequest}/print', [BarangayDocumentController::class, 'print'])->name('print');
            Route::get('/{documentRequest}/view', [BarangayDocumentController::class, 'view'])->name('view');
        });
        
        // Complaint Management (Basic)
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [BarangayComplaintController::class, 'index'])->name('index');
            Route::get('/{complaint}', [BarangayComplaintController::class, 'show'])->name('show');
            Route::post('/{complaint}/assign', [BarangayComplaintController::class, 'assign'])->name('assign');
            Route::post('/{complaint}/update-status', [BarangayComplaintController::class, 'updateStatus'])->name('update-status');
            Route::post('/{complaint}/resolve', [BarangayComplaintController::class, 'resolve'])->name('resolve');
            Route::post('/{complaint}/schedule-hearing', [BarangayComplaintController::class, 'scheduleHearing'])->name('schedule-hearing');
        });

        // Complaint Workflow (Advanced)
        Route::prefix('complaints-workflow')->name('complaints-workflow.')->group(function () {
            Route::get('/', [ComplaintWorkflowController::class, 'index'])->name('index');
            Route::get('/{complaint}', [ComplaintWorkflowController::class, 'show'])->name('show');
            Route::get('/{complaint}/print-report', [ComplaintWorkflowController::class, 'printReport'])->name('print-report');
            
            // Secretary Actions
            Route::post('/{complaint}/secretary-review', [ComplaintWorkflowController::class, 'secretaryReview'])
                ->name('secretary-review')
                ->middleware('role:barangay-secretary|barangay-staff');
            
            // Captain Actions
            Route::post('/{complaint}/captain-decision', [ComplaintWorkflowController::class, 'captainDecision'])
                ->name('captain-decision')
                ->middleware('role:barangay-captain');
            Route::post('/{complaint}/start-mediation', [ComplaintWorkflowController::class, 'startMediation'])
                ->name('start-mediation')
                ->middleware('role:barangay-captain');
            Route::post('/{complaint}/record-settlement', [ComplaintWorkflowController::class, 'recordSettlement'])
                ->name('record-settlement')
                ->middleware('role:barangay-captain');
            Route::post('/{complaint}/assign-lupon', [ComplaintWorkflowController::class, 'assignToLupon'])
                ->name('assign-lupon')
                ->middleware('role:barangay-captain');
            
            // Staff Actions
            Route::post('/{complaint}/issue-summons', [ComplaintWorkflowController::class, 'issueSummons'])
                ->name('issue-summons')
                ->middleware('role:barangay-captain|barangay-secretary|barangay-staff');
            Route::post('/{complaint}/record-appearance', [ComplaintWorkflowController::class, 'recordAppearance'])
                ->name('record-appearance')
                ->middleware('role:barangay-captain|barangay-secretary|barangay-staff');
            Route::post('/{complaint}/issue-certificate', [ComplaintWorkflowController::class, 'issueCertificate'])
                ->name('issue-certificate')
                ->middleware('role:barangay-captain|barangay-secretary|barangay-staff');
            
            // Lupon Hearing Actions
            Route::post('/{complaint}/schedule-hearing', [LuponHearingController::class, 'schedule'])
                ->name('schedule-hearing')
                ->middleware('role:barangay-captain|barangay-secretary');
            Route::get('/hearings/{hearing}', [LuponHearingController::class, 'show'])->name('hearing-show');
            Route::post('/hearings/{hearing}/start', [LuponHearingController::class, 'start'])
                ->name('hearing-start')
                ->middleware('role:barangay-captain|lupon');
            Route::post('/hearings/{hearing}/complete', [LuponHearingController::class, 'complete'])
                ->name('hearing-complete')
                ->middleware('role:barangay-captain|lupon');
        });
        
        // Business Permits
        Route::prefix('permits')->name('permits.')->group(function () {
            Route::get('/', [BarangayPermitController::class, 'index'])->name('index');
            Route::get('/{businessPermit}', [BarangayPermitController::class, 'show'])->name('show');
            Route::get('/{businessPermit}/process', [BarangayPermitController::class, 'process'])->name('process');
            Route::post('/{businessPermit}/approve', [BarangayPermitController::class, 'approve'])->name('approve');
            Route::post('/{businessPermit}/reject', [BarangayPermitController::class, 'reject'])->name('reject');
            Route::put('/{businessPermit}', [BarangayPermitController::class, 'update'])->name('update');
            Route::get('/{businessPermit}/pdf', [BarangayPermitController::class, 'downloadPDF'])->name('pdf');
            Route::get('/{businessPermit}/preview', [BarangayPermitController::class, 'previewPDF'])->name('preview');
            Route::post('/{businessPermit}/mark-inspection', [BarangayPermitController::class, 'markForInspection'])->name('mark-inspection');
            Route::post('/{businessPermit}/complete-inspection', [BarangayPermitController::class, 'completeInspection'])->name('complete-inspection');
            Route::post('/{businessPermit}/renew', [BarangayPermitController::class, 'renew'])->name('renew');
            Route::get('/export/excel', [BarangayPermitController::class, 'exportExcel'])->name('export');
        });
        
        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [BarangayReportController::class, 'index'])->name('index');
            Route::get('/residents', [BarangayReportController::class, 'residents'])->name('residents');
            Route::get('/documents', [BarangayReportController::class, 'documents'])->name('documents');
            Route::get('/complaints', [BarangayReportController::class, 'complaints'])->name('complaints');
            Route::get('/permits', [BarangayReportController::class, 'permits'])->name('permits');
            Route::get('/monthly-summary', [BarangayReportController::class, 'monthlySummary'])->name('monthly-summary');
            Route::post('/export', [BarangayReportController::class, 'export'])->name('export');
        });
        
        // Lupon Members Management
        Route::resource('lupon', BarangayLuponController::class)->only(['index', 'show']);
        
        // Barangay User Management (Captain/Secretary only)
        Route::middleware('role:barangay-captain|barangay-secretary')->prefix('users')->name('users.')->group(function () {
            Route::get('/', [BarangayUserController::class, 'index'])->name('index');
            Route::get('/create', [BarangayUserController::class, 'create'])->name('create');
            Route::post('/', [BarangayUserController::class, 'store'])->name('store');
            Route::get('/{user}', [BarangayUserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [BarangayUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [BarangayUserController::class, 'update'])->name('update');
            Route::post('/{user}/toggle-status', [BarangayUserController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{user}', [BarangayUserController::class, 'destroy'])->name('destroy')->middleware('role:barangay-captain');
        });


        // System Configuration
        Route::resource('document-types', DocumentTypeController::class);
        Route::patch('document-types/{documentType}/toggle-status', [DocumentTypeController::class, 'toggleStatus'])
             ->name('document-types.toggleStatus');
        Route::patch('document-types/{documentType}/toggle-printing', [DocumentTypeController::class, 'togglePrinting'])
             ->name('document-types.togglePrinting');
        Route::get('document-types/{documentType}/template', [DocumentTypeController::class, 'editTemplate'])
             ->name('document-types.template');
        Route::put('document-types/{documentType}/template', [DocumentTypeController::class, 'updateTemplate'])
             ->name('document-types.template.update');

        // Complaint & Permit Types
        Route::resource('complaint-types', ComplaintTypeController::class);
        Route::resource('business-permit-types', BusinessPermitTypeController::class);

        // Announcements (Captain & Secretary only)
        Route::resource('announcements', \App\Http\Controllers\Barangay\AnnouncementController::class);
        Route::patch('announcements/{announcement}/publish', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'publish'])
             ->name('announcements.publish');
        Route::patch('announcements/{announcement}/archive', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'archive'])
             ->name('announcements.archive');
        Route::patch('announcements/{announcement}/toggle-pin', [\App\Http\Controllers\Barangay\AnnouncementController::class, 'togglePin'])
             ->name('announcements.toggle-pin');

    });

    /*
    |--------------------------------------------------------------------------
    | Lupon Member Routes
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('lupon')->name('lupon.')->middleware('role:lupon-member')->group(function () {
        Route::get('/dashboard', [LuponDashboardController::class, 'index'])->name('dashboard');
        
        // Profile
        Route::prefix('profile')->group(function () {
            Route::get('/', [LuponDashboardController::class, 'profile'])->name('profile');
            Route::put('/', [LuponDashboardController::class, 'updateProfile'])->name('profile.update');
            Route::post('photo', [LuponDashboardController::class, 'updatePhoto'])->name('profile.photo');
            Route::post('password', [LuponDashboardController::class, 'changePassword'])->name('profile.password');
        });
        
        // Complaints
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [LuponComplaintController::class, 'index'])->name('index');
            Route::get('/{complaint}', [LuponComplaintController::class, 'show'])->name('show');
            Route::post('/{complaint}/schedule-hearing', [LuponComplaintController::class, 'scheduleHearing'])->name('schedule-hearing');
            Route::post('/{complaint}/record-resolution', [LuponComplaintController::class, 'recordResolution'])->name('record-resolution');
            Route::post('/{complaint}/recommend-certificate', [LuponComplaintController::class, 'recommendCertificate'])->name('recommend-certificate');
        });
        
        // Hearings
        Route::prefix('hearings')->name('hearings.')->group(function () {
            Route::get('/', [LuponHearingController::class, 'index'])->name('index');
            Route::get('/{hearing}', [LuponHearingController::class, 'show'])->name('show');
            Route::post('/{hearing}/start', [LuponHearingController::class, 'start'])->name('start');
            Route::post('/{hearing}/complete', [LuponHearingController::class, 'complete'])->name('complete');
            Route::post('/{hearing}/upload-minutes', [LuponHearingController::class, 'uploadMinutes'])->name('upload-minutes');
            Route::post('/{hearing}/postpone', [LuponHearingController::class, 'postpone'])->name('postpone');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Resident Routes (with Email Verification)
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('resident')->name('resident.')
        ->middleware(['ubms.resident', \App\Http\Middleware\EnsureEmailIsVerified::class])
        ->group(function () {
            Route::get('/dashboard', [ResidentDashboardController::class, 'index'])->name('dashboard');
            
            // Profile Management
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('/', [ResidentProfileController::class, 'show'])->name('show');
                Route::get('/create', [ResidentProfileController::class, 'create'])->name('create');
                Route::post('/', [ResidentProfileController::class, 'store'])->name('store');
                Route::get('/edit', [ResidentProfileController::class, 'edit'])->name('edit');
                Route::put('/', [ResidentProfileController::class, 'update'])->name('update');
                Route::post('/upload-id', [ResidentProfileController::class, 'uploadId'])->name('upload-id');
            });
            
            // Document Requests
            Route::prefix('documents')->name('documents.')->group(function () {
                Route::get('/', [ResidentDocumentController::class, 'index'])->name('index');
                Route::get('/create/{documentTypeSlug?}', [ResidentDocumentController::class, 'create'])->name('create');
                Route::post('/', [ResidentDocumentController::class, 'store'])->name('store');
                Route::get('/{id}', [ResidentDocumentController::class, 'show'])->name('show');
                Route::get('/{id}/download', [ResidentDocumentController::class, 'download'])->name('download');
                Route::post('/{id}/cancel', [ResidentDocumentController::class, 'cancel'])->name('cancel');
            });
            
            // Complaints
            Route::prefix('complaints')->name('complaints.')->group(function () {
                Route::get('/', [ResidentComplaintController::class, 'index'])->name('index');
                Route::get('/create', [ResidentComplaintController::class, 'create'])->name('create');
                Route::post('/', [ResidentComplaintController::class, 'store'])->name('store');
                Route::get('/{complaint}', [ResidentComplaintController::class, 'show'])->name('show');
                Route::post('/{complaint}/upload-evidence', [ResidentComplaintController::class, 'uploadEvidence'])->name('upload-evidence');
            });
        });
});