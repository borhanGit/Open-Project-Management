<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WorkPackageController;
use Illuminate\Support\Facades\Route;

// Public Documentation
Route::get('docs/{page?}', [DocController::class, 'show'])->name('docs.show');

// Guest Authentication & Password Recovery Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
    Route::get('quick-login/{email}', [AuthController::class, 'quickLogin'])->name('auth.quick-login');

    // Password Reset
    Route::get('forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}/read', [NotificationController::class, 'readAndRedirect'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // User Profile & Password Management
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Projects
    Route::resource('projects', ProjectController::class)->only(['index', 'store', 'show', 'update']);
    Route::post('projects/{project}/members', [ProjectController::class, 'addMember'])->name('projects.members.store');
    Route::get('projects/{project}/kanban', [KanbanController::class, 'show'])->name('projects.kanban');

    // Work Packages
    Route::get('projects/{project}/work-packages', [WorkPackageController::class, 'index'])->name('projects.work-packages.index');
    Route::post('projects/{project}/work-packages', [WorkPackageController::class, 'store'])->name('projects.work-packages.store');

    Route::get('work-packages', [WorkPackageController::class, 'index'])->name('work-packages.index');
    Route::post('work-packages', [WorkPackageController::class, 'store'])->name('work-packages.store');
    Route::get('work-packages/{workPackage}', [WorkPackageController::class, 'show'])->name('work-packages.show');
    Route::match(['put', 'patch'], 'work-packages/{workPackage}', [WorkPackageController::class, 'update'])->name('work-packages.update');
    Route::post('work-packages/{workPackage}/status', [WorkPackageController::class, 'updateStatus'])->name('work-packages.status');
    Route::post('work-packages/{workPackage}/comments', [WorkPackageController::class, 'addComment'])->name('work-packages.comments.store');
    Route::post('work-packages/{workPackage}/time', [WorkPackageController::class, 'logTime'])->name('work-packages.time.store');

    // User Switcher & Logout
    Route::get('switch-user/{user}', [AuthController::class, 'switchUser'])->name('auth.switch-user');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Administration & RBAC (Restricted to Administrators)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/{user}/projects', [UserController::class, 'assignProject'])->name('users.projects.assign');
        Route::delete('users/{user}/projects/{project}', [UserController::class, 'removeProject'])->name('users.projects.remove');

        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    });
});
