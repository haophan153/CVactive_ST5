<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Việc làm phù hợp với profile của bạn</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.6; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 20px; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 16px; }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; padding: 32px 28px; text-align: center; }
        .header h1 { font-size: 22px; font-weight: 800; margin-bottom: 6px; }
        .header p { opacity: 0.9; font-size: 14px; }
        .greeting { padding: 24px 28px 8px; font-size: 15px; }
        .intro { padding: 0 28px 20px; font-size: 14px; color: #64748b; }
        .job-card { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
        .job-card:last-child { border-bottom: none; }
        .job-header { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 10px; }
        .job-logo { width: 48px; height: 48px; border-radius: 10px; object-fit: contain; background: white; border: 1px solid #e2e8f0; flex-shrink: 0; padding: 4px; }
        .job-logo-placeholder { width: 48px; height: 48px; border-radius: 10px; background: #eef2ff; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800; color: #6366f1; flex-shrink: 0; }
        .job-title { font-size: 16px; font-weight: 700; color: #0f172a; line-height: 1.3; margin-bottom: 2px; }
        .job-company { font-size: 13px; color: #64748b; margin-bottom: 4px; }
        .job-meta { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-location { background: #f1f5f9; color: #475569; }
        .badge-salary { background: #ecfdf5; color: #059669; }
        .badge-new { background: #fef3c7; color: #d97706; }
        .match-score { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-left: auto; flex-shrink: 0; }
        .score-high { background: #ecfdf5; color: #059669; }
        .score-mid { background: #eff6ff; color: #2563eb; }
        .score-low { background: #f1f5f9; color: #64748b; }
        .skills { margin: 10px 0; }
        .skills-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
        .skill-tag { display: inline-block; padding: 2px 8px; background: #f1f5f9; color: #475569; border-radius: 4px; font-size: 11px; margin: 0 3px 3px 0; }
        .skill-tag.matched { background: #dcfce7; color: #166534; }
        .cta-btn { display: inline-block; background: #4f46e5; color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; transition: background 0.15s; margin-top: 8px; }
        .cta-btn:hover { background: #4338ca; }
        .footer { padding: 20px 28px; border-top: 1px solid #f1f5f9; }
        .footer-links { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
        .footer-link { color: #6366f1; text-decoration: none; font-size: 13px; }
        .footer-link:hover { text-decoration: underline; }
        .footer-unsubscribe { text-align: center; padding: 12px 28px 24px; font-size: 11px; color: #94a3b8; }
        .footer-unsubscribe a { color: #94a3b8; text-decoration: underline; }
        .powered-by { text-align: center; padding: 16px; font-size: 11px; color: #cbd5e1; }
    </style>
</head>
<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="card">
            <div class="header">
                <h1>{{ $count }} việc làm phù hợp với bạn</h1>
                <p>{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- Greeting --}}
        <div class="card">
            <div class="greeting">
                Xin chào <strong>{{ $user->name }}</strong>,
            </div>
            <div class="intro">
                Dựa trên CV của bạn, chúng tôi đã tìm thấy <strong>{{ $count }} vị trí</strong> phù hợp.
                Đây là những việc làm mới được đăng trên CVactive trong những ngày gần đây.
            </div>
        </div>

        {{-- Job cards --}}
        @foreach($matches as $match)
        @php
            $job = $match->jobPost;
            $score = $match->final_score;
            $scoreClass = $score >= 75 ? 'score-high' : ($score >= 50 ? 'score-mid' : 'score-low');
            $scoreLabel = $score >= 75 ? 'Rất phù hợp' : ($score >= 50 ? 'Phù hợp' : 'Khá phù hợp');
            $matchedSkills = array_slice($match->matched_skills ?? [], 0, 5);
            $missingSkills = array_slice($match->missing_skills ?? [], 0, 3);
        @endphp
        <div class="card">
            <div class="job-card">
                {{-- Job header --}}
                <div class="job-header">
                    @if($job->company_logo_url)
                        <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}" class="job-logo">
                    @else
                        <div class="job-logo-placeholder">{{ $job->company_initials }}</div>
                    @endif
                    <div style="flex: 1; min-width: 0;">
                        <div class="job-title">{{ $job->title }}</div>
                        <div class="job-company">{{ $job->company_name }}</div>
                        <div class="job-meta">
                            @if($job->location)
                                <span class="badge badge-location">
                                    <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $job->location }}
                                </span>
                            @endif
                            @if($job->salary_label !== 'Thương lượng')
                                <span class="badge badge-salary">{{ $job->salary_label }}</span>
                            @endif
                            @if($job->is_new)
                                <span class="badge badge-new">Mới</span>
                            @endif
                            @if($job->is_remote)
                                <span class="badge badge-location">Remote</span>
                            @endif
                        </div>
                    </div>
                    <div class="match-score {{ $scoreClass }}">
                        {{ $score }}% khớp
                    </div>
                </div>

                {{-- Skills --}}
                @if(!empty($matchedSkills) || !empty($missingSkills))
                <div class="skills">
                    @if(!empty($matchedSkills))
                        <div class="skills-label">Kỹ năng khớp</div>
                        @foreach($matchedSkills as $skill)
                            <span class="skill-tag matched">{{ $skill }}</span>
                        @endforeach
                    @endif
                    @if(!empty($missingSkills))
                        <div class="skills-label" style="margin-top: 6px;">Có thể bổ sung</div>
                        @foreach($missingSkills as $skill)
                            <span class="skill-tag">{{ $skill }}</span>
                        @endforeach
                    @endif
                </div>
                @endif

                {{-- CTA --}}
                <a href="{{ $match->jobPost->share_url }}" class="cta-btn">Xem &amp; Ứng tuyển ngay →</a>
            </div>
        </div>
        @endforeach

        {{-- Settings --}}
        <div class="card">
            <div class="footer">
                <div style="font-size: 13px; color: #64748b; text-align: center; margin-bottom: 12px;">
                    Điều chỉnh preferences hoặc tắt thông báo
                </div>
                <div class="footer-links">
                    <a href="{{ route('dashboard') }}" class="footer-link">Dashboard</a>
                    <span style="color: #cbd5e1;">·</span>
                    <a href="{{ route('dashboard') }}" class="footer-link">Cài đặt thông báo</a>
                </div>
            </div>
        </div>

        {{-- Unsubscribe --}}
        <div class="footer-unsubscribe">
            Bạn nhận email này vì đã bật Smart Job Matcher trên CVactive.<br>
            <a href="#">Tắt thông báo việc làm</a> · <a href="#">Điều chỉnh ngưỡng phù hợp</a>
        </div>

        <div class="powered-by">
            Powered by CVactive · Smart Job Matcher
        </div>

    </div>
</body>
</html>
