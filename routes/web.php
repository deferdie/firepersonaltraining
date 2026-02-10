<?php

use App\Http\Controllers\Client\Auth\AuthenticatedSessionController as ClientAuthenticatedSessionController;
use App\Http\Controllers\Client\Auth\RegisteredUserController as ClientRegisteredUserController;
use App\Http\Controllers\Client\HomeController as ClientHomeController;
use App\Http\Controllers\Client\MessagesController as ClientMessagesController;
use App\Http\Controllers\Client\ProfileController as ClientProfilePageController;
use App\Http\Controllers\Client\ProgressController as ClientProgressController;
use App\Http\Controllers\Client\WorkoutsController as ClientWorkoutsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Trainer\Auth\AuthenticatedSessionController as TrainerAuthenticatedSessionController;
use App\Http\Controllers\Trainer\Auth\RegisteredUserController as TrainerRegisteredUserController;
use App\Http\Controllers\Trainer\DashboardController as TrainerDashboardController;
use App\Http\Controllers\Trainer\ClientsController as TrainerClientsController;
use App\Http\Controllers\Trainer\ClientHabitController as TrainerClientHabitController;
use App\Http\Controllers\Trainer\ClientAssignedContentController as TrainerClientAssignedContentController;
use App\Http\Controllers\Trainer\ClientScheduleController as TrainerClientScheduleController;
use App\Http\Controllers\Trainer\ClientInvitationController as TrainerClientInvitationController;
use App\Http\Controllers\Trainer\ClientNoteController as TrainerClientNoteController;
use App\Http\Controllers\Trainer\GroupHabitController as TrainerGroupHabitController;
use App\Http\Controllers\Trainer\GroupsController as TrainerGroupsController;
use App\Http\Controllers\Trainer\MessagesController as TrainerMessagesController;
use App\Http\Controllers\Trainer\LibraryController as TrainerLibraryController;
use App\Http\Controllers\Trainer\LibraryHabitsController as TrainerLibraryHabitsController;
use App\Http\Controllers\Trainer\SettingsController as TrainerSettingsController;
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

        // Client habits (assign content)
        Route::post('/clients/{client}/habits', [TrainerClientHabitController::class, 'store'])->name('clients.habits.store');

        // Client assigned content (for scheduling)
        Route::get('/clients/{client}/assigned-content', [TrainerClientAssignedContentController::class, 'index'])->name('clients.assigned-content.index');

        // Client schedules
        Route::get('/clients/{client}/schedules', [TrainerClientScheduleController::class, 'index'])->name('clients.schedules.index');
        Route::post('/clients/{client}/schedules', [TrainerClientScheduleController::class, 'store'])->name('clients.schedules.store');
        Route::patch('/clients/{client}/schedules/{schedule}', [TrainerClientScheduleController::class, 'update'])->name('clients.schedules.update');
        Route::delete('/clients/{client}/schedules/{schedule}', [TrainerClientScheduleController::class, 'destroy'])->name('clients.schedules.destroy');

        // Groups routes
        Route::get('/groups', [TrainerGroupsController::class, 'index'])->name('groups.index');
        Route::post('/groups', [TrainerGroupsController::class, 'store'])->name('groups.store');
        Route::get('/groups/{group}', [TrainerGroupsController::class, 'show'])->name('groups.show');
        Route::post('/groups/{group}/members', [TrainerGroupsController::class, 'storeMembers'])->name('groups.members.store');
        Route::delete('/groups/{group}/members/{client}', [TrainerGroupsController::class, 'destroyMember'])->name('groups.members.destroy');

        // Group habits (assign content)
        Route::post('/groups/{group}/habits', [TrainerGroupHabitController::class, 'store'])->name('groups.habits.store');

        Route::get('/messages', [TrainerMessagesController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}', [TrainerMessagesController::class, 'show'])->name('messages.show');
        Route::post('/messages', [TrainerMessagesController::class, 'store'])->name('messages.store');
        Route::patch('/messages/{conversation}', [TrainerMessagesController::class, 'update'])->name('messages.update');
        
        Route::get('/library', [TrainerLibraryController::class, 'index'])->name('library.index');
        Route::get('/library/programs', fn () => redirect()->route('trainer.library.index'))->name('library.programs.index');
        Route::get('/library/exercises', fn () => redirect()->route('trainer.library.index'))->name('library.exercises.index');
        Route::get('/library/forms', fn () => redirect()->route('trainer.library.index'))->name('library.forms.index');
        Route::get('/library/assessments', fn () => redirect()->route('trainer.library.index'))->name('library.assessments.index');
        Route::get('/library/videos', fn () => redirect()->route('trainer.library.index'))->name('library.videos.index');
        Route::get('/library/documents', fn () => redirect()->route('trainer.library.index'))->name('library.documents.index');
        Route::get('/library/habits', [TrainerLibraryHabitsController::class, 'index'])->name('library.habits.index');
        Route::get('/library/habits/list', [TrainerLibraryHabitsController::class, 'list'])->name('library.habits.list');
        Route::post('/library/habits', [TrainerLibraryHabitsController::class, 'store'])->name('library.habits.store');
        Route::patch('/library/habits/{habit}', [TrainerLibraryHabitsController::class, 'update'])->name('library.habits.update');
        Route::delete('/library/habits/{habit}', [TrainerLibraryHabitsController::class, 'destroy'])->name('library.habits.destroy');
        Route::get('/library/meal-plans', fn () => redirect()->route('trainer.library.index'))->name('library.meal-plans.index');

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

        Route::get('/settings', [TrainerSettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [TrainerSettingsController::class, 'update'])->name('settings.update');
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
        Route::get('/home', [ClientHomeController::class, 'index'])->name('home');
        Route::get('/workouts', [ClientWorkoutsController::class, 'index'])->name('workouts');
        Route::get('/progress', [ClientProgressController::class, 'index'])->name('progress');
        Route::get('/messages/{conversation?}', [ClientMessagesController::class, 'index'])->name('messages.index');
        Route::get('/profile', [ClientProfilePageController::class, 'index'])->name('profile.index');

        Route::get('/dashboard', function (\Illuminate\Http\Request $request) {
            $tab = $request->get('tab', 'home');
            $routes = [
                'home' => 'client.home',
                'workouts' => 'client.workouts',
                'progress' => 'client.progress',
                'chat' => 'client.messages.index',
                'profile' => 'client.profile.index',
            ];

            if ($tab === 'chat' && $request->has('conversation')) {
                return redirect()->route(
                    'client.messages.index',
                    ['conversation' => $request->get('conversation')]
                );
            }

            return redirect()->route($routes[$tab] ?? 'client.home');
        })->name('dashboard');

        Route::post('/messages', [ClientMessagesController::class, 'store'])->name('messages.store');
        Route::post('/conversations/{conversation}/mark-read', [ClientMessagesController::class, 'markRead'])->name('conversations.mark-read');

        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::post('/logout', [ClientAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

