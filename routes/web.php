<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\TemplateController;
use Illuminate\Support\Facades\Route;

// ── Public routes ──────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/pricing', function () {
    $plans = \App\Models\Plan::where('is_active', true)->orderBy('price')->get();
    return view('pricing', compact('plans'));
})->name('pricing');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

Route::get('/faq', function () {
    $faqs = \App\Models\Faq::where('is_active', true)->orderBy('sort_order')->get();
    return view('faq', compact('faqs'));
})->name('faq');

Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// CV public share
Route::get('/cv/s/{token}', [CvController::class, 'share'])->name('cv.public');

// ── Authenticated + verified routes ───────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [CvController::class, 'index'])->name('dashboard');

    // CV Management
    Route::get('/cv/create', [CvController::class, 'create'])->name('cv.create');
    Route::post('/cv', [CvController::class, 'store'])->name('cv.store');
    Route::get('/cv/{cv}/edit', [CvController::class, 'edit'])->name('cv.edit');
    Route::put('/cv/{cv}', [CvController::class, 'update'])->name('cv.update');
    Route::delete('/cv/{cv}', [CvController::class, 'destroy'])->name('cv.destroy');

    // CV Sections (AJAX)
    Route::post('/cv/{cv}/sections', [CvController::class, 'saveSections'])->name('cv.sections.save');
    Route::post('/cv/{cv}/sections/add', [CvController::class, 'addSection'])->name('cv.sections.add');
    Route::delete('/cv/{cv}/sections/{section}', [CvController::class, 'deleteSection'])->name('cv.sections.delete');

    // CV Template switch
    Route::post('/cv/{cv}/template', [CvController::class, 'changeTemplate'])->name('cv.template.change');

    // CV Share & Export
    Route::post('/cv/{cv}/share', [CvController::class, 'getShareLink'])->name('cv.share');
    Route::get('/cv/{cv}/pdf', [CvController::class, 'exportPdf'])->name('cv.pdf');
    Route::get('/cv/{cv}/png', [CvController::class, 'exportPng'])->name('cv.png');

    // Templates
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
});

// ── Payment routes ────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/checkout/{plan}', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/process/{plan}', [\App\Http\Controllers\PaymentController::class, 'process'])->name('process');
    Route::get('/bank/{payment}', [\App\Http\Controllers\PaymentController::class, 'bankTransfer'])->name('bank');
    Route::get('/success/{payment}', [\App\Http\Controllers\PaymentController::class, 'success'])->name('success');
    Route::get('/cancel/{payment}', [\App\Http\Controllers\PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/history', [\App\Http\Controllers\PaymentController::class, 'history'])->name('history');
});

// Payment callbacks (no auth, but verified by signature)
Route::get('/payment/vnpay/return', [\App\Http\Controllers\PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
Route::post('/payment/vnpay/ipn', [\App\Http\Controllers\PaymentController::class, 'vnpayIpn'])->name('payment.vnpay.ipn');
Route::get('/payment/momo/return', [\App\Http\Controllers\PaymentController::class, 'momoReturn'])->name('payment.momo.return');
Route::post('/payment/momo/ipn', [\App\Http\Controllers\PaymentController::class, 'momoIpn'])->name('payment.momo.ipn');
Route::get('/payment/fail', fn() => view('payment.fail'))->name('payment.fail');

// ── Admin routes ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'store']);
    Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->except(['show']);
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class)->except(['show']);
    Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::patch('payments/{payment}/status', [\App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('payments.status');
});

require __DIR__ . '/auth.php';
