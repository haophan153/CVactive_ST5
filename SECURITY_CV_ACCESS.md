# CV Access Security Documentation

## Overview

This document describes the secure authorization system implemented for protecting CV files and job application data in the CVactive recruitment platform.

## Security Architecture

### 1. Authorization Model

**Role-Based Access Control (RBAC):**
- **Admin**: Full access to all CVs and applications
- **HR**: Can only access CVs from job posts they own
- **User**: Can only access their own applications
- **Guest**: Can only submit applications (no access to any CV data)

**Ownership Verification:**
Every CV access request must verify:
1. User is authenticated (`auth()->check()`)
2. User role is HR or Admin
3. Job Post ownership: `job_post.user_id === current_user.id`

### 2. File Storage Security

**Critical: CV files are stored in `storage/app/private/` (NOT public)**

```php
// ✅ CORRECT - File stored in private storage
$file->storeAs('applications/'.$jobPostId, $filename, 'local');
// Path: storage/app/private/applications/{jobPostId}/{filename}

// ❌ WRONG - Never do this!
// File would be publicly accessible via direct URL
$file->store('applications', 'public');
```

**Why Private Storage?**
- Files cannot be accessed via direct URL (e.g., `yourapp.com/storage/cv.pdf`)
- Access requires going through controller authorization
- Every download is logged and audited

### 3. Download Security Flow

```
User clicks "Download CV"
        ↓
Controller receives request
        ↓
[STEP 1] Check authentication → abort(401) if not logged in
        ↓
[STEP 2] Gate::allows('downloadCv', $application)
         ↓
         ├─ Is user Admin? → ALLOW
         ├─ Is user HR + owns job_post? → ALLOW
         └─ Otherwise? → abort(403) + LOG ATTEMPT
        ↓
[STEP 3] Verify file exists on disk
        ↓
[STEP 4] Log successful access (who, when, which file)
        ↓
[STEP 5] Stream file via response()->download()
         (NOT redirect to direct URL)
```

### 4. Database Schema

**Migration: `2026_03_27_000001_add_secure_cv_path_to_job_applications.php`**

| Column | Type | Description |
|--------|------|-------------|
| `cv_path` | string (nullable) | Secure path in private storage |
| `cv_file` | string (nullable) | Legacy field (public storage) |
| Index: `[job_post_id, user_id]` | | Fast ownership queries |
| Index: `[status]` | | Fast status filtering |

### 5. Policies

**ApplicationPolicy (`app/Policies/ApplicationPolicy.php`)**

| Method | Authorization Rule |
|--------|-------------------|
| `viewApplications(jobPost)` | Admin OR job post owner |
| `view(application)` | Admin OR job post owner |
| `downloadCv(application)` | Admin OR job post owner |
| `updateStatus(application)` | Admin OR job post owner |
| `delete(application)` | Admin OR job post owner |
| `searchCv(jobPost)` | Admin OR job post owner |

**JobPostPolicy (`app/Policies/JobPostPolicy.php`)**

| Method | Authorization Rule |
|--------|-------------------|
| `view(jobPost)` | Public posts: anyone. Drafts: owner/admin |
| `update(jobPost)` | Admin OR job post owner |
| `delete(jobPost)` | Admin OR job post owner |
| `publish(jobPost)` | Admin OR job post owner |
| `viewApplications(jobPost)` | Admin OR job post owner |

### 6. Logging

**Dedicated log channel: `cv_access`**

Log file: `storage/logs/cv-access.log` (rotated daily, kept 90 days)

**Events Logged:**
- CV download attempts (success & denied)
- CV file upload
- Application status changes
- Application deletion
- Signed URL generation

**Log Entry Example:**
```json
{
    "user_id": 42,
    "user_role": "hr",
    "application_id": 15,
    "candidate_name": "Nguyen Van A",
    "job_post_id": 7,
    "job_post_owner_id": 42,
    "cv_path": "applications/7/nguyen-van-a_7_a1b2c3d4.pdf",
    "authorized": true,
    "ip": "192.168.1.100",
    "user_agent": "Mozilla/5.0...",
    "timestamp": "2026-03-27T10:30:00+07:00"
}
```

### 7. Route Protection

```php
// HR routes (protected by 'hr' middleware)
Route::middleware(['auth', 'hr'])->prefix('hr')->name('hr.')->group(function () {
    // Standard HR routes...
});

// CV Download - SECURED via Policy (not just middleware)
// Why? Admin also needs to download CVs, but AdminMiddleware blocks non-admins
Route::get('/applications/{application}/cv', [JobApplicationController::class, 'downloadCv'])
    ->name('hr.applications.cv.download')
    ->middleware('auth'); // Just auth, Policy handles authorization
```

### 8. Extra Security Measures

#### 8.1 File Upload Validation
```php
'cv_file' => [
    'nullable',
    'file',
    'mimes:pdf,doc,docx',  // Only allowed file types
    'max:5120',             // 5MB max
],
```

#### 8.2 Filename Sanitization
```php
private function sanitizeFilename(string $filename): string
{
    $filename = basename($filename);           // Remove path components
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename); // Remove special chars
    if (!Str::endsWith(strtolower($filename), '.pdf')) {
        $filename .= '.pdf';                   // Ensure .pdf extension
    }
    return $filename;
}
```

#### 8.3 Unique Filename Generation
```php
$filename = sprintf(
    '%s_%s_%s.%s',
    Str::slug($validated['full_name']),  // Sanitized name
    $jobPost->id,                        // Job post ID
    Str::random(8),                      // Random suffix (collision prevention)
    $file->getClientOriginalExtension() // Original extension
);
```

#### 8.4 Signed URLs (Optional)
For sharing CV links via email (e.g., to hiring managers):
```php
// Generate URL valid for 15 minutes
$signedUrl = Storage::disk('local')->temporaryUrl(
    'private/' . $application->cv_path,
    now()->addMinutes(15)
);
```

### 9. Mass Assignment Protection

All models use `$fillable` to prevent mass assignment:

```php
class JobApplication extends Model
{
    protected $fillable = [
        'job_post_id', 'user_id', 'cv_id', 'full_name',
        'email', 'phone', 'cv_file', 'cv_path', 'cv_text',
        'cover_letter', 'status', 'notes', 'applied_at',
    ];
    // cv_path is explicitly whitelisted
    // Other dangerous fields are not fillable
}
```

### 10. Security Checklist

- [x] CV files stored in private storage (`storage/app/private/`)
- [x] No direct file URL exposure (`asset()` or direct links)
- [x] All downloads go through controller authorization
- [x] Ownership verification on every CV access
- [x] Admin access for all CVs
- [x] Mass assignment protection
- [x] File upload validation (type, size)
- [x] Filename sanitization (path traversal prevention)
- [x] CV access logging
- [x] 403 Forbidden for unauthorized access
- [x] 401 Unauthorized for unauthenticated access

### 11. Testing Authorization

**Test Case 1: HR tries to download CV from another HR's job post**
```
Expected: 403 Forbidden
Log entry: CV Download DENIED - Unauthorized Access Attempt
```

**Test Case 2: Admin downloads any CV**
```
Expected: 200 OK + file download
Log entry: CV File Downloaded Successfully
```

**Test Case 3: HR downloads CV from their own job post**
```
Expected: 200 OK + file download
Log entry: CV File Downloaded Successfully
```

**Test Case 4: Guest tries to access CV**
```
Expected: 401 Unauthorized
Log entry: (No log - authentication fails first)
```

**Test Case 5: User tries to download someone else's CV**
```
Expected: 403 Forbidden
Log entry: CV Download DENIED - Unauthorized Access Attempt
```

## Migration Commands

```bash
# Run migration
php artisan migrate

# Create a test HR user
php artisan tinker
App\Models\User::create([
    'name' => 'HR User',
    'email' => 'hr@example.com',
    'password' => Hash::make('password'),
    'role' => 'hr',
]);

# Check CV access logs
tail -f storage/logs/cv-access.log

# Clear logs
php artisan log:clear
```

## Configuration

**Logging retention (in `config/logging.php`):**
```php
'cv_access' => [
    'driver' => 'daily',
    'path' => storage_path('logs/cv-access.log'),
    'days' => env('LOG_CV_ACCESS_DAYS', 90), // Keep for 90 days
],
```

Set `LOG_CV_ACCESS_DAYS=365` in `.env` for longer retention.
