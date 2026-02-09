<?php

use App\Http\Controllers\Client\Auth\AuthenticatedSessionController as ClientAuthenticatedSessionController;
use App\Http\Controllers\Client\Auth\RegisteredUserController as ClientRegisteredUserController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Trainer\Auth\AuthenticatedSessionController as TrainerAuthenticatedSessionController;
use App\Http\Controllers\Trainer\Auth\RegisteredUserController as TrainerRegisteredUserController;
use App\Http\Controllers\Trainer\DashboardController as TrainerDashboardController;
use App\Http\Controllers\Trainer\ClientsController as TrainerClientsController;
use App\Http\Controllers\Trainer\ClientInvitationController as TrainerClientInvitationController;
use App\Http\Controllers\Trainer\ClientNoteController as TrainerClientNoteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('trainer.login') || Route::has('client.login'),
        'canRegister' => Route::has('trainer.register') || Route::has('client.register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/login', [TrainerAuthenticatedSessionController::class, 'create'])->name('login');

// Trainer authentication routes
Route::prefix('trainer')->name('trainer.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [TrainerAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [TrainerAuthenticatedSessionController::class, 'store']);
        Route::get('/register', [TrainerRegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [TrainerRegisteredUserController::class, 'store']);
    });

    Route::middleware(['auth', 'trainer'])->group(function () {
        Route::get('/dashboard', [TrainerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/logout', [TrainerAuthenticatedSessionController::class, 'destroy'])->name('logout');
        
        // Client routes
        Route::get('/clients', [TrainerClientsController::class, 'index'])->name('clients.index');
        Route::post('/clients', [TrainerClientsController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [TrainerClientsController::class, 'show'])->name('clients.show');
        Route::post('/clients/{client}/send-signup-invitation', [TrainerClientInvitationController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('clients.send-signup-invitation');
        
        // Client notes routes
        Route::post('/clients/{client}/notes', [TrainerClientNoteController::class, 'store'])->name('clients.notes.store');
        
        Route::get('/groups', function () {
            return redirect()->route('trainer.dashboard');
        })->name('groups.index');
        
        Route::get('/messages', function () {
            return redirect()->route('trainer.dashboard');
        })->name('messages.index');
        
        Route::get('/library', function () {
            return redirect()->route('trainer.dashboard');
        })->name('library.index');
        
        Route::get('/packages', function () {
            return redirect()->route('trainer.dashboard');
        })->name('packages.index');
        
        Route::get('/team', function () {
            return redirect()->route('trainer.dashboard');
        })->name('team.index');
        
        Route::get('/marketing', function () {
            return redirect()->route('trainer.dashboard');
        })->name('marketing.index');
        
        Route::get('/billing', function () {
            return redirect()->route('trainer.dashboard');
        })->name('billing.index');
        
        Route::get('/app', function () {
            return redirect()->route('trainer.dashboard');
        })->name('app.index');
        
        Route::get('/website', function () {
            return redirect()->route('trainer.dashboard');
        })->name('website.index');
    });
});

// Client authentication routes
Route::prefix('client')->name('client.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [ClientAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [ClientAuthenticatedSessionController::class, 'store']);
        Route::get('/register', [ClientRegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [ClientRegisteredUserController::class, 'store']);
    });

    Route::middleware(['auth', 'client'])->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/logout', [ClientAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});
