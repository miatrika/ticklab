<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\NotificationController;

// Routes publiques
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Changement de mot de passe obligatoire
    Route::get('/change-password', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/change-password', [PasswordChangeController::class, 'update'])->name('password.update');
    
    // Routes avec vérification du changement de mot de passe
    Route::middleware(['check.password.change'])->group(function () {
        // Dashboards
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard')->middleware('role:admin');
        
        Route::get('/technicien/dashboard', function () {
            return view('technicien.dashboard');
        })->name('technicien.dashboard')->middleware('role:technicien');
        
        // Tickets
        Route::resource('tickets', TicketController::class);
        Route::post('/tickets/{ticket}/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/tickets/{ticket}/close', [TicketController::class, 'closeTicket'])->name('tickets.close');
        Route::post('/tickets/{ticket}/reopen', [TicketController::class, 'reopenTicket'])->name('tickets.reopen');

        // Users (admin only)
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', UserController::class);
            Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        });
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });
});