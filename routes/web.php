<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\JobListingController;
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

// Contact form — throttle 5 req/min/IP chống spam
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])
    ->name('contact.store')
    ->middleware('throttle:5,1');

Route::get('/faq', [FaqController::class, 'index'])->name('faq');

Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// CV public share (no auth)
// L6: Throttle CV share để chống scan token / spam view
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/cv/s/{token}', [CvController::class, 'share'])->name('cv.public');
    Route::get('/cv/s/{token}/pdf', [CvController::class, 'exportPdfByShareToken'])
        ->middleware('throttle:10,1')   // PDF tốn tài nguyên, throttle thấp hơn
        ->name('cv.public.pdf');
    Route::get('/cv/s/{token}/png', [CvController::class, 'exportPngByShareToken'])
        ->middleware('throttle:10,1')
        ->name('cv.public.png');
});

// ── Authenticated + verified routes ───────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/heartbeat', [\App\Http\Controllers\DashboardController::class, 'heartbeat'])->name('dashboard.heartbeat');

    // Smart Job Matcher
    Route::get('/dashboard/job-alerts', [App\Http\Controllers\JobAlertController::class, 'index'])->name('dashboard.job-alerts');
    Route::post('/dashboard/job-alerts', [App\Http\Controllers\JobAlertController::class, 'store'])->name('dashboard.job-alerts.store');
    Route::delete('/dashboard/job-alerts', [App\Http\Controllers\JobAlertController::class, 'destroy'])->name('dashboard.job-alerts.destroy');
    Route::post('/dashboard/job-alerts/toggle', [App\Http\Controllers\JobAlertController::class, 'toggle'])->name('dashboard.job-alerts.toggle');
    Route::post('/dashboard/job-alerts/upload-cv', [App\Http\Controllers\JobAlertController::class, 'uploadCv'])
        ->middleware('throttle:10,1')
        ->name('dashboard.job-alerts.upload-cv');
    Route::delete('/dashboard/job-alerts/uploaded-cv/{id}', [App\Http\Controllers\JobAlertController::class, 'deleteUploadedCv'])
        ->middleware('throttle:10,1')
        ->name('dashboard.job-alerts.delete-uploaded-cv');
    Route::post('/dashboard/job-alerts/extract-skills', [App\Http\Controllers\JobAlertController::class, 'extractSkills'])->name('dashboard.job-alerts.extract-skills');

    // API: job matches widget
    Route::get('/api/job-matches', [App\Http\Controllers\JobAlertController::class, 'apiMatches'])->name('api.job-matches');
    Route::post('/api/job-matches/{jobId}/viewed', [App\Http\Controllers\JobAlertController::class, 'markViewed'])->name('api.job-matches.viewed');

    // CV Management
    Route::get('/cv/create', [CvController::class, 'create'])->name('cv.create');
    Route::post('/cv', [CvController::class, 'store'])->name('cv.store');
    Route::get('/cv/{cv}/edit', [CvController::class, 'edit'])->name('cv.edit');
    Route::put('/cv/{cv}', [CvController::class, 'update'])->name('cv.update');
    Route::delete('/cv/{cv}', [CvController::class, 'destroy'])->name('cv.destroy');

    // CV Sections (AJAX) — L2: throttle 60/min chống spam auto-save + DoS DB write
    Route::post('/cv/{cv}/sections', [CvController::class, 'saveSections'])
        ->middleware('throttle:60,1')
        ->name('cv.sections.save');
    Route::post('/cv/{cv}/sections/add', [CvController::class, 'addSection'])
        ->middleware('throttle:30,1')
        ->name('cv.sections.add');
    Route::delete('/cv/{cv}/sections/{section}', [CvController::class, 'deleteSection'])
        ->middleware('throttle:30,1')
        ->name('cv.sections.delete');

    // CV Template switch
    Route::post('/cv/{cv}/template', [CvController::class, 'changeTemplate'])->name('cv.template.change');

    // CV Avatar
    Route::post('/cv/{cv}/avatar', [CvController::class, 'uploadAvatar'])
        ->middleware('throttle:10,1')
        ->name('cv.avatar.upload');
    Route::delete('/cv/{cv}/avatar', [CvController::class, 'deleteAvatar'])->name('cv.avatar.delete');

    // CV Preview (AJAX) — throttle để chống spam render
    Route::get('/cv/{cv}/preview', [CvController::class, 'getPreview'])
        ->middleware('throttle:30,1')
        ->name('cv.preview');

    // CV Share & Export — L5: throttle vì PDF generation tốn tài nguyên
    Route::post('/cv/{cv}/share', [CvController::class, 'getShareLink'])->name('cv.share');
    Route::get('/cv/{cv}/pdf', [CvController::class, 'exportPdf'])
        ->middleware('throttle:10,1')
        ->name('cv.pdf');
    Route::get('/cv/{cv}/png', [CvController::class, 'exportPng'])
        ->middleware('throttle:10,1')
        ->name('cv.png');

    // Templates
    Route::get('/templates', [TemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/{template}/preview', [TemplateController::class, 'preview'])->name('templates.preview');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
});

// ── Payment routes ────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/checkout/{plan}', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/process/{plan}', [\App\Http\Controllers\PaymentController::class, 'process'])->name('process');
    Route::get('/bank/{token}', [\App\Http\Controllers\PaymentController::class, 'bankTransfer'])->name('bank');
    Route::get('/success/{payment}', [\App\Http\Controllers\PaymentController::class, 'success'])->name('success');
    Route::get('/cancel', [\App\Http\Controllers\PaymentController::class, 'cancel'])->name('cancel');
    Route::get('/history', [\App\Http\Controllers\PaymentController::class, 'history'])->name('history');
});

// Payment callbacks (no auth, but verified by signature)
Route::get('/payment/vnpay/return', [\App\Http\Controllers\PaymentController::class, 'vnpayReturn'])->name('payment.vnpay.return');
Route::post('/payment/vnpay/ipn', [\App\Http\Controllers\PaymentController::class, 'vnpayIpn'])->name('payment.vnpay.ipn');
Route::get('/payment/momo/return', [\App\Http\Controllers\PaymentController::class, 'momoReturn'])->name('payment.momo.return');
Route::post('/payment/momo/ipn', [\App\Http\Controllers\PaymentController::class, 'momoIpn'])->name('payment.momo.ipn');
Route::get('/payment/fail', fn() => view('payment.fail'))->name('payment.fail');

// ── Admin routes ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin', 'throttle:120,1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Global search — throttle 60 req/min chống DoS DB
    Route::get('/search', \App\Http\Controllers\Admin\SearchController::class)
        ->middleware('throttle:60,1')
        ->name('search');

    // Users
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'store']);
    Route::patch('users/{user}/quick', [\App\Http\Controllers\Admin\UserController::class, 'quickUpdate'])->name('users.quick');
    Route::post('users/bulk', [\App\Http\Controllers\Admin\UserController::class, 'bulk'])->name('users.bulk');
    Route::get('users-export', [\App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');

    // Templates
    Route::resource('templates', \App\Http\Controllers\Admin\TemplateController::class)->except(['show']);
    Route::patch('templates/{template}/toggle', [\App\Http\Controllers\Admin\TemplateController::class, 'toggle'])->name('templates.toggle');

    // Blog
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class)->except(['show']);
    Route::post('blog/bulk', [\App\Http\Controllers\Admin\BlogController::class, 'bulk'])->name('blog.bulk');

    // Blog Categories
    Route::resource('blog-categories', \App\Http\Controllers\Admin\BlogCategoryController::class)
        ->except(['show'])
        ->parameters(['blog-categories' => 'blogCategory']);

    // FAQ
    Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class)->except(['show']);
    Route::patch('faqs/{faq}/toggle', [\App\Http\Controllers\Admin\FaqController::class, 'toggle'])->name('faqs.toggle');
    Route::post('faqs/bulk', [\App\Http\Controllers\Admin\FaqController::class, 'bulk'])->name('faqs.bulk');

    // Payments
    Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::patch('payments/{payment}/status', [\App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('payments.status');
    Route::post('payments/bulk-status', [\App\Http\Controllers\Admin\PaymentController::class, 'bulkStatus'])->name('payments.bulk-status');
    Route::get('payments-export', [\App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('payments.export');

    // Plans
    Route::resource('plans', \App\Http\Controllers\Admin\PlanController::class)->except(['show']);
    Route::patch('plans/{plan}/toggle', [\App\Http\Controllers\Admin\PlanController::class, 'toggle'])->name('plans.toggle');

    // Job Posts (admin overview)
    Route::get('job-posts', [\App\Http\Controllers\Admin\JobPostController::class, 'index'])->name('job-posts.index');
    Route::get('job-posts/{jobPost}', [\App\Http\Controllers\Admin\JobPostController::class, 'show'])->name('job-posts.show');
    Route::patch('job-posts/{jobPost}/toggle', [\App\Http\Controllers\Admin\JobPostController::class, 'toggle'])->name('job-posts.toggle');
    Route::delete('job-posts/{jobPost}', [\App\Http\Controllers\Admin\JobPostController::class, 'destroy'])->name('job-posts.destroy');

    // Contacts
    Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class)->only(['index', 'show', 'destroy']);
    Route::patch('contacts/{contact}/toggle-read', [\App\Http\Controllers\Admin\ContactController::class, 'toggleRead'])->name('contacts.toggle-read');
    Route::post('contacts/bulk', [\App\Http\Controllers\Admin\ContactController::class, 'bulk'])->name('contacts.bulk');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});

// ── HR routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'hr', 'throttle:180,1'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/job-posts', [JobPostController::class, 'index'])->name('job-posts.index');
    Route::get('/job-posts/heartbeat', [JobPostController::class, 'heartbeat'])->name('job-posts.heartbeat');
    Route::get('/job-posts/create', [JobPostController::class, 'create'])->name('job-posts.create');
    Route::post('/job-posts', [JobPostController::class, 'store'])->name('job-posts.store');
    Route::get('/job-posts/{jobPost}', [JobPostController::class, 'show'])->name('job-posts.show');
    Route::get('/job-posts/{jobPost}/edit', [JobPostController::class, 'edit'])->name('job-posts.edit');
    Route::put('/job-posts/{jobPost}', [JobPostController::class, 'update'])->name('job-posts.update');
    Route::delete('/job-posts/{jobPost}', [JobPostController::class, 'destroy'])->name('job-posts.destroy');
    Route::post('/job-posts/{jobPost}/publish', [JobPostController::class, 'publish'])->name('job-posts.publish');
    Route::post('/job-posts/{jobPost}/close', [JobPostController::class, 'close'])->name('job-posts.close');

    // Tìm kiếm CV ứng viên theo kỹ năng/kinh nghiệm
    // C5: throttle searchCv để chống HR spam kéo hệ thống extract PDF liên tục
    Route::get('/job-posts/{jobPost}/search-cv', [App\Http\Controllers\JobApplicationController::class, 'searchCv'])
        ->middleware('throttle:30,1')
        ->name('job-posts.search-cv');

    // Ứng viên theo từng bài đăng
    Route::get('/job-posts/{jobPost}/applications', [App\Http\Controllers\JobApplicationController::class, 'hrApplicationsByJob'])->name('job-posts.applications');

    // Quản lý ứng viên (tất cả)
    Route::get('/applications', [App\Http\Controllers\JobApplicationController::class, 'hrIndex'])->name('applications.index');
    Route::get('/applications/{application}', [App\Http\Controllers\JobApplicationController::class, 'hrShow'])->name('applications.show');
    Route::patch('/applications/{application}', [App\Http\Controllers\JobApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    Route::delete('/applications/{application}', [App\Http\Controllers\JobApplicationController::class, 'destroy'])->name('applications.destroy');

    // ============================================================
    // SECURE CV DOWNLOAD - Critical security route
    // ============================================================
    // Route này KHÔNG có middleware 'hr' vì:
    // 1. Admin cũng cần tải CV được
    // 2. Authorization được xử lý bên trong controller qua Gate/Policy
    // 3. Đảm bảo chỉ chủ sở hữu job post mới tải được CV
    // L7: throttle 30 lượt tải/phút/user để chống spam download + đầy disk
    Route::get('/applications/{application}/cv', [App\Http\Controllers\JobApplicationController::class, 'downloadCv'])
        ->name('applications.cv.download')
        ->middleware(['auth', 'throttle:30,1']);

    // Optional: Temporary signed URL cho CV (dùng trong email)
    Route::get('/applications/{application}/cv-url', [App\Http\Controllers\JobApplicationController::class, 'getSignedUrl'])
        ->name('applications.cv.url')
        ->middleware('auth');

    // AI CV Scoring — L4: throttle chống spam đốt tiền OpenAI
    Route::post('/job-posts/{jobPost}/ai-score', [App\Http\Controllers\Hr\AiScoreController::class, 'bulkScore'])
        ->name('job-posts.ai-score')
        ->middleware('throttle:5,1');
    Route::post('/applications/{application}/rescore', [App\Http\Controllers\Hr\AiScoreController::class, 'rescore'])
        ->name('applications.rescore')
        ->middleware('throttle:10,1');
});

// Public job listings (advanced filter page)
// L1: Throttle job listings + apply để chống spam đơn ứng tuyển
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/jobs', [JobListingController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{jobPost}', [JobListingController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{jobPost}/apply', [App\Http\Controllers\JobApplicationController::class, 'apply'])
        ->middleware('throttle:3,1')   // Mỗi user chỉ được apply 3 lần/phút cho 1 job
        ->name('jobs.apply');
});

// User routes - lịch sử ứng tuyển
Route::middleware(['auth', 'verified'])->prefix('my-applications')->name('my-applications.')->group(function () {
    Route::get('/', [App\Http\Controllers\JobApplicationController::class, 'myApplications'])->name('index');
});

require __DIR__ . '/auth.php';
