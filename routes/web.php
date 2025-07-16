<?php
/*
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
*/
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AbcDirectoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReportController;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login',  [AuthController::class, 'store']);
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
});

// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/requests/create',  [RequestController::class, 'create'])->name('requests.create');
    Route::post('/requests',        [RequestController::class, 'store'])->name('requests.store');
    Route::get('/requests',        [RequestController::class, 'index'])->name('requests.index');
});


Route::get('/complaints/create',  [ComplaintController::class, 'create'])->name('complaints.create');
Route::post('/complaints',        [ComplaintController::class, 'store'])->name('complaints.store');
Route::get('/complaints',         [ComplaintController::class, 'index'])->name('complaints.index');
Route::get('/lupon',         [ComplaintController::class, 'lupon'])->name('complaints.lupon');

Route::get('/notifications', [NotificationController::class, '__invoke'])
      ->name('notifications')->middleware('auth');

Route::get('/reports', [ReportController::class, 'index'])
      ->name('reports.index')->middleware('auth');

Route::get('/abcdirectory', [AbcDirectoryController::class, 'index'])
      ->name('abc.index')->middleware('auth');

Route::get('/archive', [AdminController::class, 'archive'])
      ->name('officials.archive')->middleware('auth');
Route::view('offline', 'admin.offline')->name('offline');
Route::view('logs-demo', 'admin.logs')->middleware('auth')->name('logs.demo');
Route::view('/profile', 'resident.profile')->middleware('auth')->name('profile');
Route::view('/permits', 'permits.index')->middleware('auth')->name('permits.index');
Route::view('/settings/notifications', 'settings.notifications')
      ->middleware('auth')
      ->name('settings.notifications');

Route::view('/captain', 'admin.dashboard')
      ->middleware('auth')    // and your 'captain' gate if you have one
      ->name('admin.dashboard');


Route::view('/residents', 'resident.index')->middleware('auth')->name('resident.list');