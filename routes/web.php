<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\AdminController;

// Public
Route::get('/', [TenantController::class, 'home'])->name('home');
Route::get('/negocio/{slug}', [TenantController::class, 'show'])->name('tenant.show');
Route::get('/negocio/{slug}/agendar', [AppointmentController::class, 'booking'])->name('booking');
Route::get('/api/tenants/{slug}/profissionais/{professionalId}/slots', [AppointmentController::class, 'slots']);

// API
Route::get('/api/tenants/{slug}', [TenantController::class, 'apiShow']);
Route::get('/api/tenants', [TenantController::class, 'apiSearch']);
Route::get('/api/tenants/{slug}/profissionais', [ProfessionalController::class, 'apiByTenant']);
Route::get('/api/tenants/{slug}/servicos', [ServiceController::class, 'apiByTenant']);

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Customer
Route::middleware(['auth'])->group(function () {
    Route::get('/minha-conta', [AuthController::class, 'dashboard'])->name('customer.dashboard');
    Route::post('/api/appointments', [AppointmentController::class, 'store']);
    Route::post('/api/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);
    Route::post('/api/reviews', [ReviewController::class, 'store']);
});

// Manager
Route::middleware(['auth', 'role:manager,admin,super_admin'])->prefix('/gestao/{tenantId}')->group(function () {
    Route::get('/agenda', [AppointmentController::class, 'managerDashboard'])->name('manager.dashboard');
    Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
});

// Business Admin
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('/admin/{tenantId}')->group(function () {
    Route::get('/', [AdminController::class, 'businessDashboard'])->name('business.admin');
    Route::resource('profissionais', ProfessionalController::class);
    Route::resource('servicos', ServiceController::class);
    Route::post('horarios', [ProfessionalController::class, 'updateHours']);
});

// Platform Admin
Route::middleware(['auth', 'role:super_admin'])->prefix('/plataforma')->group(function () {
    Route::get('/', [AdminController::class, 'platformDashboard'])->name('platform.admin');
    Route::get('/tenants', [AdminController::class, 'tenants']);
    Route::post('/tenants', [AdminController::class, 'createTenant']);
    Route::get('/planos', [PlanController::class, 'index']);
    Route::post('/planos', [PlanController::class, 'store']);
});

// Booking flow API
Route::post('/api/appointments/verify', [AppointmentController::class, 'verifySlot']);
