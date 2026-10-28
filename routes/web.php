<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    $artists = \App\Models\Artist::withCount('reviews')->take(3)->get();
    $recentReviews = \App\Models\Review::with(['user', 'artist'])->latest()->take(3)->get();
    
    // Fetch some portfolio images for each category to show in the service hover
    $categoryImages = \App\Models\Artist::all()->groupBy('category')->map(function($artists) {
        return $artists->pluck('portfolio_images')->flatten()->filter()->take(5)->values();
    });

    return view('public.index', compact('artists', 'recentReviews', 'categoryImages'));
})->name('public.index');

use App\Http\Controllers\Admin\ArtistController;

use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;

Route::resource('appointments', AppointmentController::class)->middleware(['auth', 'role:staff,admin']);

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('artists', ArtistController::class);
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/report/print', [PaymentController::class, 'print'])->name('payments.print');
        
        // Admin Appointment Management
        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments', [AdminAppointmentController::class, 'store'])->name('appointments.store');
        Route::post('/appointments/{id}/validate', [AdminAppointmentController::class, 'validateBooking'])->name('appointments.validate');
        Route::delete('/appointments/{id}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::get('/appointments/calendar/events', [AdminAppointmentController::class, 'calendar'])->name('appointments.calendar');

        // New Management Modules
        Route::get('/staff/create', [AdminController::class, 'createStaff'])->name('staff.create');
        Route::post('/staff/store', [AdminController::class, 'storeStaff'])->name('staff.store');
        Route::get('/customers', [AdminController::class, 'customers'])->name('customers.index');
        Route::get('/staff/oversight', [AdminController::class, 'oversight'])->name('staff.oversight');
        Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory.index');
        Route::get('/pos', [AdminController::class, 'pos'])->name('pos');
        Route::post('/send-email', [AdminController::class, 'sendEmail'])->name('send-email');
        
        // Staff Management Actions
        Route::delete('/staff/{id}', [AdminController::class, 'destroyStaff'])->name('staff.destroy');
        Route::post('/staff/{id}/password', [AdminController::class, 'updateStaffPassword'])->name('staff.password.update');
        Route::post('/staff/link-artist', [AdminController::class, 'linkStaffToArtist'])->name('staff.link-artist');

        // POS & Appointment Integration
        Route::post('/pos/checkout', [AdminController::class, 'posCheckout'])->name('pos.checkout');
        Route::post('/appointments/{id}/complete', [AdminAppointmentController::class, 'complete'])->name('appointments.complete');
        Route::post('/appointments/{id}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');

        // Schedule Management
        Route::get('/schedule-requests', [AdminController::class, 'scheduleRequests'])->name('schedule-requests.index');
        Route::post('/schedule-requests/{id}/update', [AdminController::class, 'updateScheduleRequest'])->name('schedule-requests.update');

        // Service Management
        Route::get('/services', [AdminController::class, 'services'])->name('services.index');
        Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
        Route::put('/services/{id}', [AdminController::class, 'updateService'])->name('services.update');
        Route::delete('/services/{id}', [AdminController::class, 'destroyService'])->name('services.destroy');

        // Admin Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings.index');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.password');

        // Queue & Availability Management
        Route::post('/artists/{id}/live-status', [AdminAppointmentController::class, 'updateArtistStatus'])->name('artists.live-status');
        Route::post('/queue/{id}/action', [AdminAppointmentController::class, 'updateQueueAction'])->name('queue.action');
        Route::post('/queue/walkin', [AdminAppointmentController::class, 'addQueueWalkin'])->name('queue.walkin');
        Route::get('/queue/data', [AdminAppointmentController::class, 'getQueueData'])->name('queue.data');

        // Admin Inquiries & Quotations
        Route::get('/inquiries', [\App\Http\Controllers\InquiryController::class, 'adminIndex'])->name('inquiries.index');
        Route::get('/inquiries/{id}', [\App\Http\Controllers\InquiryController::class, 'adminShow'])->name('inquiries.show');
        Route::post('/inquiries/{id}/message', [\App\Http\Controllers\InquiryController::class, 'adminSendMessage'])->name('inquiries.message');
        Route::post('/inquiries/{id}/quotation', [\App\Http\Controllers\InquiryController::class, 'adminStoreQuotation'])->name('inquiries.quotation.store');
    });
});

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/home', [CustomerController::class, 'home'])->name('home');
    Route::get('/book', [CustomerController::class, 'book'])->name('book');
    Route::post('/book', [CustomerController::class, 'storeBooking'])->name('book.store');
    Route::get('/appointments', [CustomerController::class, 'appointments'])->name('appointments');
    Route::get('/artists', [CustomerController::class, 'artists'])->name('artists');
    Route::get('/artists/{id}/portfolio', [CustomerController::class, 'artistPortfolio'])->name('artists.portfolio');
    Route::get('/services', [CustomerController::class, 'services'])->name('services');
    Route::get('/reviews', [CustomerController::class, 'reviews'])->name('reviews');
    Route::get('/settings', [CustomerController::class, 'settings'])->name('settings');
    Route::post('/settings', [CustomerController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/password', [CustomerController::class, 'updatePassword'])->name('settings.password');
    
    // Appointment Actions
    Route::post('/appointments/{id}/reschedule', [CustomerController::class, 'rescheduleAppointment'])->name('appointments.reschedule');
    Route::post('/appointments/{id}/cancel', [CustomerController::class, 'cancelAppointment'])->name('appointments.cancel');
    Route::post('/reviews', [CustomerController::class, 'submitReview'])->name('reviews.store');

    // Customer Live Queue Actions
    Route::post('/queue/join', [CustomerController::class, 'joinQueue'])->name('queue.join');
    Route::post('/queue/{id}/cancel', [CustomerController::class, 'cancelQueue'])->name('queue.cancel');
    Route::get('/queue/ticket', [CustomerController::class, 'getQueueTicket'])->name('queue.ticket');

    // Customer Inquiries & Quotations
    Route::get('/inquiries', [\App\Http\Controllers\InquiryController::class, 'customerIndex'])->name('inquiries.index');
    Route::get('/inquiries/create', [\App\Http\Controllers\InquiryController::class, 'customerCreate'])->name('inquiries.create');
    Route::post('/inquiries', [\App\Http\Controllers\InquiryController::class, 'customerStore'])->name('inquiries.store');
    Route::get('/inquiries/{id}', [\App\Http\Controllers\InquiryController::class, 'customerShow'])->name('inquiries.show');
    Route::post('/inquiries/{id}/message', [\App\Http\Controllers\InquiryController::class, 'customerSendMessage'])->name('inquiries.message');
    Route::post('/quotations/{id}/respond', [\App\Http\Controllers\InquiryController::class, 'customerRespondQuotation'])->name('quotations.respond');
});

use App\Http\Controllers\StaffController;
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/appointments', [StaffController::class, 'appointments'])->name('appointments');
    Route::post('/schedule-request', [StaffController::class, 'submitScheduleRequest'])->name('schedule-request.submit');
    Route::get('/appointments/calendar/events', [StaffController::class, 'calendar'])->name('appointments.calendar');
    Route::get('/portfolio', [StaffController::class, 'portfolio'])->name('portfolio');
    Route::post('/portfolio', [StaffController::class, 'updatePortfolio'])->name('portfolio.update');
    Route::post('/appointments/{id}/status', [StaffController::class, 'updateStatus'])->name('appointments.status');

    // Staff Inquiries & Quotations
    Route::get('/inquiries', [\App\Http\Controllers\InquiryController::class, 'staffIndex'])->name('inquiries.index');
    Route::get('/inquiries/{id}', [\App\Http\Controllers\InquiryController::class, 'staffShow'])->name('inquiries.show');
    Route::post('/inquiries/{id}/message', [\App\Http\Controllers\InquiryController::class, 'staffSendMessage'])->name('inquiries.message');
    Route::post('/inquiries/{id}/quotation', [\App\Http\Controllers\InquiryController::class, 'staffStoreQuotation'])->name('inquiries.quotation.store');

    // Staff Live Availability & Queue Controls
    Route::post('/live-status', [StaffController::class, 'updateLiveStatus'])->name('live-status.update');
    Route::post('/working-hours', [StaffController::class, 'updateWorkingHours'])->name('working-hours.update');
    Route::post('/queue/{id}/action', [StaffController::class, 'updateQueueAction'])->name('queue.action');
    Route::post('/queue/walkin', [StaffController::class, 'addQueueWalkin'])->name('queue.walkin');
    Route::get('/queue/data', [StaffController::class, 'getQueueData'])->name('queue.data');

    // Staff Settings
    Route::get('/settings', [StaffController::class, 'settings'])->name('settings.index');
    Route::post('/settings', [StaffController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/password', [StaffController::class, 'updatePassword'])->name('settings.password');
});

use App\Http\Controllers\VerificationController;
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/available-slots', [CustomerController::class, 'getAvailableSlots'])->name('available-slots');
    Route::get('/artists/{id}/queue-info', [CustomerController::class, 'getArtistQueueInfo'])->name('artists.queue-info');

    // In-app Notifications
    Route::get('/notifications', [\App\Http\Controllers\InquiryController::class, 'getNotifications'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\InquiryController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\InquiryController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth')->name('login.submit');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth')->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->middleware(['guest', 'throttle:auth'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware(['guest', 'throttle:auth'])->name('password.update');
