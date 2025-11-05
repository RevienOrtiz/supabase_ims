<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\SeniorController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('index');
});

// Authentication Routes
Route::get('/Login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/Login', [AuthController::class, 'login'])->name('login.post');
Route::get('/Signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/Signup', [AuthController::class, 'register'])->name('signup.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 6-Digit Code Verification Routes
Route::get('/verify-code', [AuthController::class, 'showVerifyCode'])->name('verify.code');
Route::post('/verify-code', [AuthController::class, 'verifyCode'])->name('verify.code.post');
Route::post('/resend-code', [AuthController::class, 'resendCode'])->name('resend.code');

// Google OAuth Routes
Route::get('/auth/google', [App\Http\Controllers\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::get('/authentication', function () {
    return view('login.authentication');
})->name('authentication');


// Barangay-specific stats API (protected)
Route::get('/api/barangay-stats/{barangay}', function ($barangay) {
    try {
        Log::info('API call received for barangay: ' . $barangay);
        $dashboardService = app(\App\Services\DashboardService::class);
        $stats = $dashboardService->getStatisticsByBarangay($barangay);
        Log::info('API response generated successfully for barangay: ' . $barangay);
        return response()->json($stats);
    } catch (\Exception $e) {
        Log::error('API error for barangay ' . $barangay . ': ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
})->middleware(['auth'])->name('api.barangay.stats');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/Dashboard', function () {
        $dashboardService = app(\App\Services\DashboardService::class);
        $stats = $dashboardService->getStatistics();
        $eventsByType = $dashboardService->getEventStatisticsByType();
        $events = \App\Models\Event::upcoming()
            ->orderBy('event_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
        
        return response()
            ->view('dashboard', compact('stats', 'events', 'eventsByType'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    })->name('dashboard');


    //Sidebar

    // Senior CRUD Routes
    Route::get('/View_senior/{id}', [SeniorController::class, 'show'])->name('view_senior');
    Route::get('/Edit_senior/{id}', [SeniorController::class, 'edit'])->name('edit_senior');
    Route::put('/Edit_senior/{id}', [SeniorController::class, 'update'])->name('edit_senior.update');
    Route::delete('/Delete_senior/{id}', [SeniorController::class, 'destroy'])->name('delete_senior');

    // Event Routes
    Route::get('/Events', [App\Http\Controllers\EventController::class, 'index'])->name('events');
    Route::get('/Events/create', [App\Http\Controllers\EventController::class, 'create'])->name('events.create');
    Route::post('/Events', [App\Http\Controllers\EventController::class, 'store'])->name('events.store');
    Route::get('/Events/{id}', [App\Http\Controllers\EventController::class, 'show'])->name('events.show');
    Route::get('/Events/{id}/edit', [App\Http\Controllers\EventController::class, 'edit'])->name('events.edit');
    Route::put('/Events/{id}', [App\Http\Controllers\EventController::class, 'update'])->name('events.update');
    Route::delete('/Events/{id}', [App\Http\Controllers\EventController::class, 'destroy'])->name('events.destroy');
    // Calendar API route moved to routes/api.php for public access
    
    // Event Participation Routes
    Route::get('/Events/{id}/participants', [App\Http\Controllers\EventController::class, 'participants'])->name('events.participants');
    Route::post('/Events/{id}/register', [App\Http\Controllers\EventController::class, 'registerParticipant'])->name('events.register');
    Route::delete('/Events/{id}/participants/{seniorId}', [App\Http\Controllers\EventController::class, 'removeParticipant'])->name('events.remove-participant');
    Route::put('/Events/{id}/participants/{seniorId}/attendance', [App\Http\Controllers\EventController::class, 'updateAttendance'])->name('events.update-attendance');

    Route::get('/Seniors', [SeniorController::class, 'index'])->name('seniors');
    Route::get('/Seniors/pension-report', [SeniorController::class, 'generatePensionReport'])->name('seniors.pension.report');
    
    // App Account Creation
    Route::get('/Seniors/{id}/app-account/create', [SeniorController::class, 'createAppAccount'])->name('senior.app_account.create');
    Route::post('/Seniors/{id}/app-account', [SeniorController::class, 'storeAppAccount'])->name('senior.app_account.store');
    
    // App Account Password Change
    Route::get('/Seniors/{id}/app-account/edit', [SeniorController::class, 'editAppAccount'])->name('senior.app_account.edit');
    Route::post('/Seniors/{id}/app-account/update', [SeniorController::class, 'updateAppAccount'])->name('senior.app_account.update');
    
    // Senior Citizen Submenu Routes
    Route::get('/Seniors/benefits', [SeniorController::class, 'benefits'])->name('seniors.benefits');
    Route::get('/Seniors/pension', [SeniorController::class, 'pension'])->name('seniors.pension');
    Route::get('/Seniors/id-applications', [SeniorController::class, 'idApplications'])->name('seniors.id-applications');

    //Forms
    Route::get('/Form_existing_senior', function () {
        return view('forms.form_existing_senior');
    })->name('form_existing_senior');
    Route::post('/Form_existing_senior', [App\Http\Controllers\ApplicationController::class, 'storeBenefitsApplication'])->name('forms.benefits.store');

    Route::get('/Form_pension', function () {
        return view('forms.form_pension');
    })->name('form_pension');
    Route::post('/Form_pension', [App\Http\Controllers\ApplicationController::class, 'storePensionApplication'])->name('forms.pension.store');

    Route::get('/Form_seniorID', function () {
        return view('forms.form_seniorID');
    })->name('form_seniorID');
    Route::post('/Form_seniorID', [App\Http\Controllers\ApplicationController::class, 'storeSeniorIdApplication'])->name('forms.seniorID.store');


    // Admin Profile
    Route::get('/admin/profile', function () {
        return view('admin.profile');
    })->name('admin.profile');

    // Password Reset Request Management
    Route::prefix('admin/password-reset-requests')->name('admin.password-reset-requests.')->group(function () {
        Route::get('/', [App\Http\Controllers\PasswordResetRequestController::class, 'index'])->name('index');
        Route::get('/{passwordResetRequest}', [App\Http\Controllers\PasswordResetRequestController::class, 'show'])->name('show');
        Route::post('/{passwordResetRequest}/approve', [App\Http\Controllers\PasswordResetRequestController::class, 'approve'])->name('approve');
        Route::post('/{passwordResetRequest}/reject', [App\Http\Controllers\PasswordResetRequestController::class, 'reject'])->name('reject');
        Route::delete('/{passwordResetRequest}/resolve', [App\Http\Controllers\PasswordResetRequestController::class, 'resolve'])->name('resolve');
    });

    // Table-specific edit routes
    Route::get('/view-pension/{id}', [SeniorController::class, 'viewPension'])->name('seniors.pension.view');
    Route::get('/edit-pension/{id}', [SeniorController::class, 'editPension'])->name('seniors.pension.edit');
    Route::put('/edit-pension/{id}', [SeniorController::class, 'updatePension'])->name('seniors.pension.update');
    Route::delete('/delete-pension-application/{id}', [SeniorController::class, 'deletePensionApplication'])->name('seniors.pension.delete');

    Route::get('/view-benefits/{id}', [SeniorController::class, 'viewBenefits'])->name('seniors.benefits.view');
    Route::get('/edit-benefits/{id}', [SeniorController::class, 'editBenefits'])->name('seniors.benefits.edit');
    Route::put('/edit-benefits/{id}', [SeniorController::class, 'updateBenefits'])->name('seniors.benefits.update');
    Route::delete('/delete-benefits-application/{id}', [SeniorController::class, 'deleteBenefitsApplication'])->name('seniors.benefits.delete');

    Route::get('/view-id-application/{id}', [SeniorController::class, 'viewSeniorId'])->name('seniors.id-application.view');
    Route::get('/edit-id-application/{id}', [SeniorController::class, 'editIdApplication'])->name('seniors.id-application.edit');
    Route::put('/edit-id-application/{id}', [SeniorController::class, 'updateIdApplication'])->name('seniors.id.update');
    Route::delete('/delete-id-application/{id}', [SeniorController::class, 'deleteIdApplication'])->name('seniors.id-application.delete');

    // API route for senior search
    Route::get('/api/seniors', function() {
        return \App\Models\Senior::orderBy('last_name')->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'middle_name', 'osca_id', 'barangay', 'sex', 'date_of_birth']);
    });

    // API route for fetching benefits application data
    Route::get('/api/seniors/{id}/benefits-application', function($id) {
        $benefitsApp = \App\Models\BenefitsApplication::where('senior_id', $id)->first();
        
        if ($benefitsApp) {
            return response()->json([
                'success' => true,
                'benefitsApplication' => $benefitsApp
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No benefits application found'
            ]);
        }
    });

    // OLD ROUTES - DEPRECATED (Use add_new_senior route instead)
    // for testing purposes only
    // Route::get('/masterprofile/{id}', function ($id) {
    //     try {
    //         $senior = \App\Models\Senior::findOrFail($id);
    //         Log::info('Master profile route called', [
    //             'id' => $id,
    //             'senior_id' => $senior->id,
    //             'senior_name' => $senior->first_name . ' ' . $senior->last_name
    //         ]);
    //         return view('test.masterprofile', compact('senior'));
    //     } catch (\Exception $e) {
    //         Log::error('Master profile route error', [
    //             'id' => $id,
    //             'error' => $e->getMessage()
    //         ]);
    //         return redirect()->route('seniors')->with('error', 'Senior not found.');
    //     }
    // })->name('masterprofile.show');
    
    // Default master profile route (shows empty form for adding new senior)
    // Route::get('/masterprofile', function () {
    //     return view('test.masterprofile', ['senior' => null]);
    // })->name('masterprofile');
    
    // Store new senior route
    Route::post('/seniors', [SeniorController::class, 'store'])->name('seniors.store');
    
    // Add new senior form route
    Route::get('/add-new-senior', function () {
        return view('seniors.add_new_senior');
    })->name('add_new_senior');
});

// OCR Processing Route
Route::post('/ocr/process', [App\Http\Controllers\Api\OCRController::class, 'process']);

// Legacy logout route (keeping for compatibility)
Route::get('/logout', function () {
    // Clear any session data
    session()->flush();
    // Redirect to main page
    return redirect('/');
});