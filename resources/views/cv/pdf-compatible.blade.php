{{--
    PDF-Compatible Universal CV Template
    Uses ONLY table-based layouts, solid colors, no flexbox/gradient/absolute
    Compatible with domPDF, mpdf, and all PHP PDF libraries
    Variable: $cv
--}}
@php
    $personal    = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#7F1D1D';
    $sections    = $cv->sections ?? collect();
    $fullName    = $personal['full_name'] ?? 'Họ và Tên';
    $email       = $personal['email'] ?? '';
    $phone       = $personal['phone'] ?? '';
    $address     = $personal['address'] ?? '';
    $website     = $personal['website'] ?? '';
    $linkedin    = $personal['linkedin'] ?? '';
    $github      = $personal['github'] ?? '';
    $avatar      = $personal['avatar'] ?? '';

    // Resolve avatar to base64 data URI for domPDF
    $avatarSrc = '';
    if ($avatar) {
        $projectRoot = realpath(base_path());
        
        // Helper to find avatar file and return base64
        $findAvatarBase64 = function($filename) use ($projectRoot) {
            $paths = [
                $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $filename,
                $projectRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $filename,
                public_path('storage' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $filename),
            ];
            
            foreach ($paths as $path) {
                if (file_exists($path)) {
                    $mime = 'image/webp';
                    if (preg_match('/\.jpe?g$/i', $path)) $mime = 'image/jpeg';
                    elseif (preg_match('/\.png$/i', $path)) $mime = 'image/png';
                    elseif (preg_match('/\.gif$/i', $path)) $mime = 'image/gif';
                    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
                }
            }
            return null;
        };
        
        // Extract filename from various formats
        $filename = null;
        if (preg_match('#avatars[/\\\\]([^/\?]+)#i', $avatar, $m)) {
            $filename = $m[1];
        } elseif (preg_match('#/storage/avatars/([^/\?]+)#', $avatar, $m)) {
            $filename = $m[1];
        } elseif (!str_contains($avatar, '/') && !str_contains($avatar, '\\')) {
            $filename = $avatar;
        }
        
        if ($filename) {
            $avatarSrc = $findAvatarBase64($filename);
        }
    }
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>{{ $fullName }} – CV</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'dejavusans', sans-serif; font-size: 12px; color: #1f2937; }
        .cv-page { width: 210mm; min-height: 297mm; }
        .header-bar { background-color: {{ $themeColor }}; padding: 28px 40px; }
        .header-row { width: 100%; }
        .avatar-cell { width: 90px; vertical-align: middle; padding-right: 20px; }
        .avatar-img { 
            width: 80px; 
            height: 80px; 
            border-radius: 9999px; 
            border: 3px solid rgba(255,255,255,0.5); 
            display: block; 
            object-fit: cover;
            -webkit-border-radius: 9999px;
        }
        .header-info { color: white; vertical-align: middle; }
        .header-name { font-size: 22px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header-obj { font-size: 11px; margin-top: 6px; font-style: italic; opacity: 0.88; }
        .contact-bar { background-color: #f8f9fa; padding: 8px 40px; border-bottom: 3px solid {{ $themeColor }}; font-size: 10px; color: #555; }
        .body-content { padding: 24px 40px; }
        .section { margin-bottom: 18px; }
        .section-title { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1.5px; color: {{ $themeColor }}; border-bottom: 2px solid {{ $themeColor }}; padding-bottom: 4px; margin-bottom: 10px; }
        .entry { margin-bottom: 12px; padding: 10px 12px; border-left: 3px solid #e5e7eb; }
        .entry-pos { font-size: 13px; font-weight: bold; color: #111827; }
        .entry-date { font-size: 10px; color: #888; font-style: italic; }
        .entry-company { font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 2px; }
        .entry-desc { font-size: 11px; color: #4b5563; margin-top: 6px; line-height: 1.6; }
        .skill-tag { font-size: 11px; color: white; background-color: {{ $themeColor }}; padding: 3px 10px; margin: 2px 4px 2px 0; display: inline-block; }
        .cert-entry { margin-bottom: 8px; padding: 8px 12px; border-left: 2px solid #e5e7eb; }
        .cert-name { font-size: 12px; font-weight: bold; color: #111827; }
        .cert-issuer { font-size: 11px; color: #555; }
        .cert-date { font-size: 11px; color: #888; }
        .proj-entry { margin-bottom: 12px; padding: 10px 12px; border-left: 2px solid #e5e7eb; }
        .proj-name { font-size: 12px; font-weight: bold; color: #111827; }
        .proj-url { font-size: 10px; color: {{ $themeColor }}; }
        .proj-tech { font-size: 11px; color: #888; font-style: italic; margin-top: 2px; }
        .proj-desc { font-size: 11px; color: #4b5563; margin-top: 4px; line-height: 1.6; }
        .act-entry { margin-bottom: 8px; padding: 8px 12px; border-left: 2px solid #e5e7eb; }
        .act-name { font-size: 12px; font-weight: bold; color: #111827; }
        .act-org { font-size: 11px; color: {{ $themeColor }}; font-style: italic; }
        .ref-entry { margin-bottom: 10px; padding: 8px 12px; background-color: #f9fafb; border-left: 3px solid {{ $themeColor }}; }
        .ref-name { font-size: 12px; font-weight: bold; color: #111827; }
        .ref-title { font-size: 11px; color: {{ $themeColor }}; font-weight: 600; }
        .ref-contact { font-size: 11px; color: #555; margin-top: 2px; }
    </style>
</head>
<body>
<div class="cv-page">

    {{-- HEADER --}}
    <div class="header-bar">
        <table class="header-row" cellpadding="0" cellspacing="0" border="0">
            <tr>
                @if($avatarSrc)
                <td class="avatar-cell">
                    <img class="avatar-img" src="{{ $avatarSrc }}" alt="{{ $fullName }}">
                </td>
                @endif
                <td class="header-info">
                    <div class="header-name">{{ $fullName }}</div>
                    @if($cv->objective)
                    <div class="header-obj">"{{ $cv->objective }}"</div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- Contact row --}}
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top: 14px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.2);">
            <tr>
                <td style="font-size: 10px; color: rgba(255,255,255,0.9);">
                    @php $contactParts = []; @endphp
                    @if($email) @php $contactParts[] = $email @endphp @endif
                    @if($phone) @php $contactParts[] = $phone @endphp @endif
                    @if($address) @php $contactParts[] = $address @endphp @endif
                    @if($website) @php $contactParts[] = $website @endphp @endif
                    @if($linkedin) @php $contactParts[] = 'LinkedIn: '.$linkedin @endphp @endif
                    @if($github) @php $contactParts[] = 'GitHub: '.$github @endphp @endif
                    {{ implode('  |  ', $contactParts) }}
                </td>
            </tr>
        </table>
    </div>

    {{-- BODY --}}
    <div class="body-content">

        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif
            @if($section->items->isEmpty()) @continue @endif

            <div class="section">
                <div class="section-title">{{ $section->title }}</div>

                @if($section->type === 'experience')
                    @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="entry">
                        <tr>
                            <td>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td><span class="entry-pos">{{ $c['position'] ?? '' }}</span></td>
                                        <td align="right"><span class="entry-date">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span></td>
                                    </tr>
                                </table>
                                <div class="entry-company">{{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' | ' : '' }}{{ $c['location'] ?? '' }}</div>
                                @if(!empty($c['description']))<div class="entry-desc">{{ $c['description'] }}</div>@endif
                            </td>
                        </tr>
                    </table>
                    @endforeach

                @elseif($section->type === 'education')
                    @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="entry">
                        <tr>
                            <td>
                                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td><span class="entry-pos">{{ $c['degree'] ?? '' }}</span></td>
                                        <td align="right"><span class="entry-date">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span></td>
                                    </tr>
                                </table>
                                <div class="entry-company">{{ $c['school'] ?? '' }}</div>
                                @if(!empty($c['gpa']))<div class="entry-desc">GPA: {{ $c['gpa'] }}</div>@endif
                            </td>
                        </tr>
                    </table>
                    @endforeach

                @elseif($section->type === 'skills')
                    <div>
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span class="skill-tag">
                                {{ $s['name'] ?? '' }}
                                @if(!empty($s['level']))
                                    @switch($s['level'])
                                        @case('beginner') ★☆☆☆ @break
                                        @case('intermediate') ★★☆☆ @break
                                        @case('advanced') ★★★☆ @break
                                        @case('expert') ★★★★ @break
                                    @endswitch
                                @endif
                            </span>
                        @endforeach
                    </div>

                @elseif($section->type === 'certifications')
                    @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp
                    <div class="cert-entry">
                        <span class="cert-name">{{ $c['name'] ?? '' }}</span>
                        @if(!empty($c['issuer']))<span class="cert-issuer"> — {{ $c['issuer'] }}</span>@endif
                        @if(!empty($c['date']))<span class="cert-date" style="float:right;">{{ $c['date'] }}</span>@endif
                    </div>
                    @endforeach

                @elseif($section->type === 'projects')
                    @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp
                    <div class="proj-entry">
                        <span class="proj-name">{{ $c['name'] ?? '' }}</span>
                        @if(!empty($c['url']))<span class="proj-url"> — {{ $c['url'] }}</span>@endif
                        @if(!empty($c['tech']))<div class="proj-tech">{{ $c['tech'] }}</div>@endif
                        @if(!empty($c['description']))<div class="proj-desc">{{ $c['description'] }}</div>@endif
                    </div>
                    @endforeach

                @elseif($section->type === 'activities')
                    @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp
                    <div class="act-entry">
                        <span class="act-name">{{ $c['name'] ?? '' }}</span>
                        @if(!empty($c['organization']))<span class="act-org"> — {{ $c['organization'] }}</span>@endif
                        @if(!empty($c['period']))<span style="font-size:11px;color:#888;float:right;">{{ $c['period'] }}</span>@endif
                    </div>
                    @endforeach

                @elseif($section->type === 'references')
                    @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp
                    <div class="ref-entry">
                        <div class="ref-name">{{ $c['name'] ?? '' }}</div>
                        @if(!empty($c['title']))<div class="ref-title">{{ $c['title'] }}</div>@endif
                        <div class="ref-contact">{{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' | ' : '' }}{{ $c['phone'] ?? '' }}</div>
                    </div>
                    @endforeach

                @else
                    @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp
                    <p style="font-size: 12px; color: #555; line-height: 1.7;">{{ $c['text'] ?? '' }}</p>
                    @endforeach
                @endif
            </div>
        @endforeach

    </div>
</div>
</body>
</html>
