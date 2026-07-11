# CVactive

> Professional CV builder, AI-powered CV scoring, job board, and Smart Job Matcher — built with Laravel 12.

CVactive is an all-in-one career platform that lets candidates build resumes, HR teams post and score CVs, and recruiters match candidates to jobs through both rule-based and AI-assisted pipelines.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Maintenance](https://img.shields.io/badge/maintained-yes-brightgreen)](#)

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the App](#running-the-app)
- [Scheduled Jobs](#scheduled-jobs)
- [Project Structure](#project-structure)
- [Smart Job Matcher](#smart-job-matcher)
- [AI CV Scoring](#ai-cv-scoring)
- [Payment Integration](#payment-integration)
- [Deployment Notes](#deployment-notes)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

---

## Features

### For Candidates
- **CV Builder** with multiple professional templates
- Export to **PDF** (DOMPDF + mPDF) and **PNG**
- **Google OAuth** sign-in (Laravel Socialite)
- **Two-Factor Authentication** (2FA)
- **Smart Job Matcher** — receive daily/weekly/instant job recommendations via email
- **CV upload** with AI-extracted skill profiles

### For HR / Recruiters
- **Job post management** (create, edit, publish, archive)
- **AI CV Scoring** — automatic scoring of submitted CVs against job requirements
- **Candidate dashboard** with filtering, shortlist, status tracking
- **VNPay payment** integration for premium job posts

### For Administrators
- **Admin dashboard** with content management
- **Blog** with categories and tags
- **FAQ** management
- **Plan / Pricing** management
- **Contact submissions** inbox
- **Template gallery** management for CV themes

### Platform-wide
- Multi-section Blade templates + Vite-compiled assets
- Queue-backed background jobs (database driver)
- File-based sessions, optional Redis/Memcached cache
- Security headers middleware
- Bcrypt password hashing (12 rounds)

---

## Tech Stack

| Layer | Technology |
|---|---|
| **Framework** | Laravel 12.x |
| **Language** | PHP 8.2+ |
| **Database** | MySQL (default) / SQLite supported |
| **Frontend** | Blade + Tailwind CSS 3 + Alpine.js |
| **Build tool** | Vite 7 |
| **PDF generation** | DOMPDF, mPDF |
| **PDF parsing** | Smalot/PdfParser |
| **Image processing** | Intervention/Image |
| **Authentication** | Laravel Breeze + Socialite (Google) |
| **AI / LLM** | OpenAI GPT-4o-mini (configurable) |
| **Payment** | VNPay sandbox/production |
| **Queue** | Database driver (default) |
| **Cache** | Database / File / Redis / Memcached |

---

## Quick Start

### Prerequisites

- PHP **8.2** or higher
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.0+ (or SQLite 3)
- Git

### 1. Clone the repository

```bash
git clone https://github.com/<your-org>/cvactive.git
cd cvactive
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and fill in your database credentials, mail settings, OAuth keys, and OpenAI key. See [Configuration](#configuration) below.

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Seed the database (optional)

```bash
php artisan db:seed
```

### 7. Build frontend assets

```bash
npm run build      # production
# or
npm run dev        # watch mode
```

### 8. Start the dev server

```bash
php artisan serve
```

Visit **http://127.0.0.1:8000**.

---

## Configuration

All runtime configuration lives in `.env`. **Never commit this file** — `.gitignore` already excludes it. Use `.env.example` as a template.

### Required variables

| Variable | Purpose |
|---|---|
| `APP_KEY` | Laravel encryption key (generate with `php artisan key:generate`) |
| `APP_URL` | Public base URL (e.g. `https://cvactive.com`) |
| `DB_*` | Database connection (host, port, name, user, password) |
| `MAIL_*` | SMTP credentials for transactional emails |
| `OPENAI_API_KEY` | Powers AI CV scoring and Smart Job Matcher |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | Google OAuth sign-in |

### Optional variables

| Variable | Purpose |
|---|---|
| `VNPAY_*` / `VNP_*` | VNPay payment gateway credentials |
| `AWS_*` | S3-compatible storage (used for uploaded CVs at scale) |
| `REDIS_*` | Redis cache/queue driver |
| `MEMCACHED_HOST` | Memcached cache driver |
| `QUEUE_CONNECTION` | `sync` for dev, `database` / `redis` for production |

### Generating secrets

```bash
php artisan key:generate            # APP_KEY
php artisan jwt:secret              # if JWT is installed
```

### Gmail SMTP setup (development)

1. Enable 2-Step Verification on the Google account
2. Visit https://myaccount.google.com/apppasswords
3. Create an App Password for "Mail / Other (CVactive)"
4. Set `MAIL_USERNAME` and `MAIL_PASSWORD` in `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Database Setup

### MySQL (recommended)

```bash
mysql -u root -p -e "CREATE DATABASE cvactive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cvactive
DB_USERNAME=root
DB_PASSWORD=your_password
```

### SQLite (quick local dev)

```bash
touch database/database.sqlite
```

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

### Run migrations + seeders

```bash
php artisan migrate --seed
```

---

## Running the App

### Development (built-in PHP server)

```bash
php artisan serve
# Visit http://127.0.0.1:8000
```

### Development with queue worker (recommended for Smart Job Matcher)

Open two terminals:

```bash
# Terminal 1 — web server
php artisan serve

# Terminal 2 — queue worker
php artisan queue:work

# Terminal 3 — asset watcher (optional)
npm run dev
```

### Production (Nginx + PHP-FPM)

Sample Nginx server block:

```nginx
server {
    listen 80;
    server_name cvactive.com;
    root /var/www/cvactive/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## Scheduled Jobs

CVactive uses Laravel's scheduler for daily job-match emails. The schedule is registered in `routes/console.php`.

### Local development

```bash
php artisan schedule:work
```

This runs the scheduler in the foreground and triggers every-minute checks. Useful for testing.

### Production (Linux cron)

Add a single cron entry:

```cron
* * * * * cd /var/www/cvactive && php artisan schedule:run >> /dev/null 2>&1
```

### Production (Windows Task Scheduler)

Create a task that runs every minute:

```
Program: C:\php\php.exe
Arguments: artisan schedule:run
Working directory: C:\inetpub\cvactive
```

### Inspect the schedule

```bash
php artisan schedule:list
```

---

## Project Structure

```
cvactive/
├── app/
│   ├── Console/             # Artisan commands
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/       # Admin dashboard controllers
│   │   │   ├── Api/         # JSON API endpoints
│   │   │   ├── Auth/        # Login / register / OAuth callbacks
│   │   │   ├── Hr/          # HR job-post management
│   │   │   └── JobAlertController.php  # Smart Job Matcher UI
│   │   └── Middleware/      # SecurityHeaders, etc.
│   ├── Jobs/
│   │   ├── ExtractCvTextJob.php
│   │   └── SendDailyJobAlerts.php    # Daily matcher dispatch
│   ├── Models/              # Eloquent models (User, JobAlert, JobPost, ...)
│   ├── Notifications/
│   │   └── JobMatchAlert.php         # Email template
│   ├── Providers/
│   └── Services/
│       ├── CvScoring/       # AI CV scoring pipeline
│       └── JobMatching/     # Smart Job Matcher (Rule + AI)
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/          # 38+ migrations
│   └── seeders/
├── public/                  # Web root (Vite assets compiled here)
├── resources/
│   ├── css/
│   ├── js/
│   └── views/               # Blade templates
├── routes/
│   ├── console.php          # Scheduled tasks
│   └── web.php              # Web routes
├── storage/
└── tests/
```

---

## Smart Job Matcher

The Smart Job Matcher is CVactive's core differentiator. It sends users personalized job recommendations based on their skill profile and CV content.

### Pipeline

1. **Profile extraction** — `SkillExtractor` parses the user's CV and stored skill profile to derive keyword + skill vectors.
2. **Rule-based scoring** — `RuleBasedMatcher` produces a baseline score using keyword overlap, location, salary range, and recency.
3. **AI-assisted scoring** — `AiMatcher` (OpenAI GPT-4o-mini) re-ranks the top candidates using semantic understanding of job descriptions and CVs.
4. **Top-N selection** — `JobMatcherService::matchForAlert()` returns the highest-scoring matches (typically 3–10).
5. **Delivery** — `JobMatchAlert` notification emails the candidate; `JobMatchLog` records the dispatch.

### Tuning frequency

User alerts support three cadences:

| Frequency | Trigger |
|---|---|
| `instant` | Run when user logs in (within last 24 h) |
| `daily` | Run by scheduled job at 08:00 |
| `weekly` | Run by scheduled job on Mondays |

### Manual trigger

To send alerts immediately (e.g. for testing):

```bash
php artisan tinker
>>> App\Jobs\SendDailyJobAlerts::dispatch();
```

Or fire the job directly:

```bash
php artisan tinker
>>> $alert = App\Models\JobAlert::find(1);
>>> app(App\Services\JobMatching\JobMatcherService::class)->matchForAlert($alert);
```

---

## AI CV Scoring

When a candidate uploads a CV for a job post, `app/Services/CvScoring/AiScorer.php` runs a multi-step pipeline:

1. Parse PDF/DOCX → text
2. Extract skills, experience, education
3. Score against the job post's required skills + experience level
4. Persist score + reasoning to the database
5. Display score in the HR dashboard

### Required OpenAI key

```env
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini   # default, override to gpt-4o for higher accuracy
OPENAI_TIMEOUT=15
```

### Cost control

CV scoring is rate-limited per user via the `ai_cv_score` cache key. Default cache TTL is 1 hour. To disable caching during testing:

```env
CACHE_STORE=array
```

---

## Payment Integration

CVactive integrates with **VNPay** for premium job-post purchases.

### Sandbox setup

```env
VNPAY_TMN_CODE=your_sandbox_tmn_code
VNPAY_HASH_SECRET=your_sandbox_hash_secret
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL="${APP_URL}/payment/vnpay/return"
```

Test cards are documented at https://sandbox.vnpayment.vn.

---

## Deployment Notes

### Pre-deployment checklist

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` points to your domain
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `npm run build` (compiled assets in `public/build/`)
- [ ] Storage symlink: `php artisan storage:link`
- [ ] Queue worker running (supervisor / systemd)
- [ ] Scheduler cron entry installed
- [ ] `chmod -R 775 storage bootstrap/cache` + correct ownership

### Recommended production stack

- **Web server**: Nginx 1.24+ with PHP-FPM
- **PHP**: 8.2 with OPcache enabled
- **Database**: MySQL 8.0 with daily backups
- **Queue**: Supervisor-managed `php artisan queue:work`
- **Cache**: Redis 7
- **SSL**: Let's Encrypt via Certbot

### Queue worker (Supervisor)

`/etc/supervisor/conf.d/cvactive-worker.conf`:

```ini
[program:cvactive-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/cvactive/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/cvactive/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cvactive-worker:*
```

---

## Troubleshooting

### "SQLSTATE[HY000] [2002] No connection could be made"

Database is unreachable. Check `DB_HOST`, `DB_PORT`, and that MySQL is running.

```bash
# macOS / Linux
sudo systemctl status mysql

# Windows
net start MySQL80
```

### Mail fails with "Authentication failed"

For Gmail, ensure you're using an **App Password**, not your account password. See [Gmail SMTP setup](#gmail-smtp-setup-development).

### Frontend assets are missing

Run `npm run build` (or `npm run dev` in another terminal).

### OpenAI returns 429 Too Many Requests

You exceeded the rate limit. Lower `OPENAI_TIMEOUT` retries aren't auto-handled — consider throttling CV scoring:

```php
// In AiScorer::score()
usleep(200_000); // 200ms throttle per request
```

### Schedule never fires

Check the cron entry:

```bash
crontab -l | grep cvactive
```

For local dev, use `php artisan schedule:work` instead of cron.

---

## Contributing

Contributions are welcome. Please:

1. Fork the repository
2. Create a feature branch: `git checkout -b feat/your-feature`
3. Follow PSR-12 coding standards
4. Run `composer test` before pushing
5. Submit a pull request describing the change

For major changes, open an issue first to discuss the approach.

---

## Security

If you discover a security vulnerability, please email the maintainers directly instead of opening a public issue.

---

## License

MIT License. See [LICENSE](LICENSE) for details.

---

## Credits

Built by the CVactive team. Powered by [Laravel](https://laravel.com), [Tailwind CSS](https://tailwindcss.com), and [OpenAI](https://openai.com).