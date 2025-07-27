<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SimplePasswordResetController;
use App\Http\Controllers\SimplePasswordChangeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManageFilesController;
use App\Http\Controllers\Admin\ManageRequestController;
use App\Http\Controllers\Admin\ManageReturnController;
use App\Http\Controllers\Admin\ReturnController;
use App\Http\Controllers\Admin\RequestHistoryController;
use App\Http\Controllers\Admin\ManageUserController;
use App\Http\Controllers\Admin\AddFilesController;
use App\Http\Controllers\Admin\EditFilesController;
use App\Http\Controllers\Admin\RequestStatusController;


Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Simple Password Reset Routes
Route::get('/forgot-password', [SimplePasswordResetController::class, 'showForgotForm'])
    ->name('password.request');
Route::post('/forgot-password', [SimplePasswordResetController::class, 'sendResetLink'])
    ->name('simple.password.email');
Route::get('/reset-password/{token}', [SimplePasswordResetController::class, 'showResetForm'])
    ->name('simple.password.reset');
Route::post('/reset-password', [SimplePasswordResetController::class, 'resetPassword'])
    ->name('simple.password.update');

// Simple Password Change Routes (single page)
Route::get('/change-password', [SimplePasswordChangeController::class, 'showChangeForm'])
    ->name('simple.password.change.form');
Route::post('/change-password', [SimplePasswordChangeController::class, 'changePassword'])
    ->name('simple.password.change');

// Fallback route for /home - redirect to appropriate dashboard
Route::get('/home', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Pka':
                return redirect()->route('pka.dashboard');
            default:
                return redirect()->route('admin.dashboard');
        }
    }
    return redirect()->route('login');
})->middleware('auth');

// Admin routes
Route::prefix('admin')
     ->name('admin.')
     ->middleware('auth')
     ->group(function () {

         // Routes accessible by both Admin and Pka roles
         Route::middleware(\App\Http\Middleware\RedirectIfNotRole::class.':Admin,Pka')->group(function () {
             // GET /admin/dashboard
             Route::get('dashboard', [DashboardController::class, 'index'])
                  ->name('dashboard');

             // GET /admin/manage-requests
             Route::get('/manageRequest', [ManageRequestController::class, 'index'])->name('manageRequest');
             Route::get('/requests/data', [ManageRequestController::class, 'getRequestsData'])->name('requests.data');
             Route::get('/requestFileForm/{fileID}', [ManageRequestController::class, 'requestFileForm'])->name('requestFileForm');
             Route::post('/submitFileRequest/{fileID}', [ManageRequestController::class, 'submitFileRequest'])->name('submitFileRequest');
             Route::get('search-staff', [ManageRequestController::class, 'searchStaff'])->name('searchStaff');
             
             // Reservation routes
             Route::get('/reserveFileForm/{fileID}', [ManageRequestController::class, 'reserveFileForm'])->name('reserveFileForm');
             Route::post('/submitFileReservation/{fileID}', [ManageRequestController::class, 'submitFileReservation'])->name('submitFileReservation');

             // GET /admin/request-status
             Route::get('request-status', [\App\Http\Controllers\Admin\RequestStatusController::class, 'index'])->name('requestStatus');
             // GET /admin/request-status/data (for DataTables)
             Route::get('request-status/data', [\App\Http\Controllers\Admin\RequestStatusController::class, 'data'])->name('requestStatus.data');
             // GET /admin/request-status/{requestID}/info (for getting reservation info)
             Route::get('request-status/{requestID}/info', [\App\Http\Controllers\Admin\RequestStatusController::class, 'getRequestInfo'])->name('requestStatus.info');
             // POST routes for reservation actions
             Route::post('request-status/{requestID}/cancel', [\App\Http\Controllers\Admin\RequestStatusController::class, 'cancelReservation'])->name('requestStatus.cancel');
             Route::post('request-status/{requestID}/proceed', [\App\Http\Controllers\Admin\RequestStatusController::class, 'proceedReservation'])->name('requestStatus.proceed');

             // GET /admin/request-history
             Route::get('manage-return', [ReturnController::class, 'index'])
                  ->name('manageReturn');

             Route::get('manage-return/data', [ManageReturnController::class, 'getReturnsData'])->name('manageReturn.data');
             Route::put('manage-return/{id}/update-date', [ManageReturnController::class, 'updateReturnDate'])->name('manageReturn.updateDate');
             Route::post('manage-return/{id}/process-return', [ManageReturnController::class, 'processReturn'])->name('manageReturn.processReturn');
             Route::get('manage-return/{id}/staff-details', [ManageReturnController::class, 'getStaffDetails'])->name('manageReturn.staffDetails');
             Route::get('manage-return/{id}', [ManageReturnController::class, 'getReturnDetails'])->name('manageReturn.details'); // Add this new route
         
             // GET /admin/request-history
             Route::get('request-history', [RequestHistoryController::class, 'index'])
                  ->name('requestHistory');
             Route::get('request-history/data', [RequestHistoryController::class, 'getHistoryData'])
                  ->name('requestHistory.data');
             Route::post('request-history/export', [RequestHistoryController::class, 'exportData'])
                  ->name('requestHistory.export');

             // Profile Management Routes
             Route::get('manage-profile', [\App\Http\Controllers\ManageProfileController::class, 'index'])
                   ->name('manageProfile');
             Route::put('manage-profile', [\App\Http\Controllers\ManageProfileController::class, 'update'])
                   ->name('updateProfile');
             Route::delete('manage-profile/remove-picture', [\App\Http\Controllers\ManageProfileController::class, 'removeProfilePicture'])
                   ->name('removeProfilePicture');
         });

         // Routes accessible only by Admin role
         Route::middleware(\App\Http\Middleware\RedirectIfNotRole::class.':Admin')->group(function () {
             // Add function selection page before manage files
             Route::get('select-function', [\App\Http\Controllers\FunctionController::class, 'showFunctions'])->name('selectFunction');

             // GET /admin/manage-files
             Route::get('manage-files', [ManageFilesController::class, 'index'])
                  ->name('manageFiles');
             Route::get('manage-files/data', [ManageFilesController::class, 'getFilesData'])->name('files.data');

             // File Management Routes
              Route::delete('manage-files/{id}', [ManageFilesController::class, 'deleteFile'])->name('deleteFile');

              // Add File Routes
              Route::get('add-file', [AddFilesController::class, 'create'])->name('ffaddFile');
              Route::post('add-file', [AddFilesController::class, 'store'])->name('storeFile');
              Route::get('get-activities/{functionCode}', [AddFilesController::class, 'getActivities'])->name('getActivities');
              Route::get('get-sub-activities/{activityCode}', [AddFilesController::class, 'getSubActivities'])->name('getSubActivities');
              Route::post('add-function', [AddFilesController::class, 'addFunction'])->name('addFunction');
              Route::post('add-activity', [AddFilesController::class, 'addActivity'])->name('addActivity');
              Route::post('add-sub-activity', [AddFilesController::class, 'addSubActivity'])->name('addSubActivity');

              // Edit File Routes
              Route::get('edit-file/{id}', [EditFilesController::class, 'edit'])->name('editFile');
              Route::put('edit-file/{id}', [EditFilesController::class, 'update'])->name('updateFile');

             // User Management Routes
             Route::get('manage-users', [ManageUserController::class, 'index'])
                   ->name('manageUser');
                   
             // Add these specific routes for user operations
             Route::post('users/store', [ManageUserController::class, 'store'])
                   ->name('users.store');
             Route::put('users/{user}/update', [ManageUserController::class, 'update'])
                   ->name('users.update');
             Route::delete('users/{user}/delete', [ManageUserController::class, 'destroy'])
                   ->name('users.destroy');
         });
     });

// Pka routes - same functionality as admin but with /pka/ URLs
Route::prefix('pka')
     ->name('pka.')
     ->middleware('auth')
     ->middleware(\App\Http\Middleware\RedirectIfNotRole::class.':Pka')
     ->group(function () {
         // GET /pka/dashboard
         Route::get('dashboard', [DashboardController::class, 'index'])
              ->name('dashboard');

         // GET /pka/manage-requests
         Route::get('/manageRequest', [ManageRequestController::class, 'index'])->name('manageRequest');
         Route::get('/requests/data', [ManageRequestController::class, 'getRequestsData'])->name('requests.data');
         Route::get('/requestFileForm/{fileID}', [ManageRequestController::class, 'requestFileForm'])->name('requestFileForm');
         Route::post('/submitFileRequest/{fileID}', [ManageRequestController::class, 'submitFileRequest'])->name('submitFileRequest');
         Route::get('search-staff', [ManageRequestController::class, 'searchStaff'])->name('searchStaff');
         
         // Reservation routes
         Route::get('/reserveFileForm/{fileID}', [ManageRequestController::class, 'reserveFileForm'])->name('reserveFileForm');
         Route::post('/submitFileReservation/{fileID}', [ManageRequestController::class, 'submitFileReservation'])->name('submitFileReservation');

         // GET /pka/request-status
         Route::get('request-status', [\App\Http\Controllers\Admin\RequestStatusController::class, 'index'])->name('requestStatus');
         Route::get('request-status/data', [\App\Http\Controllers\Admin\RequestStatusController::class, 'data'])->name('requestStatus.data');
         Route::get('request-status/{requestID}/info', [\App\Http\Controllers\Admin\RequestStatusController::class, 'getRequestInfo'])->name('requestStatus.info');
         Route::post('request-status/{requestID}/cancel', [\App\Http\Controllers\Admin\RequestStatusController::class, 'cancelReservation'])->name('requestStatus.cancel');
         Route::post('request-status/{requestID}/proceed', [\App\Http\Controllers\Admin\RequestStatusController::class, 'proceedReservation'])->name('requestStatus.proceed');

         // GET /pka/manage-return
         Route::get('manage-return', [ReturnController::class, 'index'])
              ->name('manageReturn');
         Route::get('manage-return/data', [ManageReturnController::class, 'getReturnsData'])->name('manageReturn.data');
         Route::put('manage-return/{id}/update-date', [ManageReturnController::class, 'updateReturnDate'])->name('manageReturn.updateDate');
         Route::post('manage-return/{id}/process-return', [ManageReturnController::class, 'processReturn'])->name('manageReturn.processReturn');
         Route::get('manage-return/{id}/staff-details', [ManageReturnController::class, 'getStaffDetails'])->name('manageReturn.staffDetails');
         Route::get('manage-return/{id}', [ManageReturnController::class, 'getReturnDetails'])->name('manageReturn.details');
     
         // GET /pka/request-history
         Route::get('request-history', [RequestHistoryController::class, 'index'])
              ->name('requestHistory');
         Route::get('request-history/data', [RequestHistoryController::class, 'getHistoryData'])
              ->name('requestHistory.data');
         Route::post('request-history/export', [RequestHistoryController::class, 'exportData'])
              ->name('requestHistory.export');

         // Profile Management Routes
         Route::get('manage-profile', [\App\Http\Controllers\ManageProfileController::class, 'index'])
               ->name('manageProfile');
         Route::put('manage-profile', [\App\Http\Controllers\ManageProfileController::class, 'update'])
               ->name('updateProfile');
         Route::delete('manage-profile/remove-picture', [\App\Http\Controllers\ManageProfileController::class, 'removeProfilePicture'])
               ->name('removeProfilePicture');
     });