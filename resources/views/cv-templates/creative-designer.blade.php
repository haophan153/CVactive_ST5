{{--
    Creative Designer CV Template – Two-column with colored sidebar
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#8B5CF6';
    $font       = $cv->font_family ?? 'Montserrat';
    $sections   = $cv->sections ?? collect();
    $fullName   = $personal['full_name'] ?? 'Họ và Tên';
    $email      = $personal['email'] ?? '';
    $phone      = $personal['phone'] ?? '';
    $address    = $personal['address'] ?? '';
    $website    = $personal['website'] ?? '';
    $linkedin   = $personal['linkedin'] ?? '';
    $github     = $personal['github'] ?? '';
    $avatar     = $personal['avatar'] ?? '';

    // Separate skills/certifications for sidebar
    $sidebarTypes  = ['skills', 'certifications', 'references', 'activities'];
    $mainTypes     = ['experience', 'education', 'projects', 'custom'];
    $sidebarSecs   = $sections->filter(fn($s) => $s->is_visible && in_array($s->type, $sidebarTypes));
    $mainSecs      = $sections->filter(fn($s) => $s->is_visible && !in_array($s->type, array_merge(['personal','objective'], $sidebarTypes)));
@endphp

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; display: flex; min-height: 297mm;">

    {{-- LEFT SIDEBAR --}}
    <div style="width: 220px; flex-shrink: 0; background: {{ $themeColor }}; padding: 36px 24px; color: white; display: flex; flex-direction: column; gap: 0;">

        {{-- Avatar + Name --}}
        <div style="text-align: center; margin-bottom: 24px;">
            @if($avatar)
            <div style="width: 88px; height: 88px; border-radius: 50%; overflow: hidden; border: 3px solid rgba(255,255,255,0.4); margin: 0 auto 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @else
            <div style="width: 88px; height: 88px; border-radius: 50%; background: rgba(255,255,255,0.2); margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; color: rgba(255,255,255,0.8);">
                {{ mb_strtoupper(mb_substr($fullName, 0, 1)) }}
            </div>
            @endif
            <h1 style="font-size: 16px; font-weight: 700; color: white; margin: 0; line-height: 1.3;">{{ $fullName }}</h1>
        </div>

        {{-- Contact info --}}
        <div style="margin-bottom: 24px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">
            <h3 style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: rgba(255,255,255,0.6); margin: 0 0 12px 0;">Liên hệ</h3>
            @if($email)
            <div style="font-size: 11px; color: rgba(255,255,255,0.85); margin-bottom: 8px; word-break: break-all;">✉ {{ $email }}</div>
            @endif
            @if($phone)
            <div style="font-size: 11px; color: rgba(255,255,255,0.85); margin-bottom: 8px;">📱 {{ $phone }}</div>
            @endif
            @if($address)
            <div style="font-size: 11px; color: rgba(255,255,255,0.85); margin-bottom: 8px;">📍 {{ $address }}</div>
            @endif
            @if($website)
            <div style="font-size: 11px; color: rgba(255,255,255,0.85); margin-bottom: 8px; word-break: break-all;">🌐 {{ $website }}</div>
            @endif
            @if($linkedin)
            <div style="font-size: 11px; color: rgba(255,255,255,0.85); margin-bottom: 8px;">in {{ $linkedin }}</div>
            @endif
            @if($github)
            <div style="font-size: 11px; color: rgba(255,255,255,0.85); margin-bottom: 8px;">⚡ {{ $github }}</div>
            @endif
        </div>

        {{-- Sidebar Sections --}}
        @foreach($sidebarSecs as $section)
        <div style="margin-bottom: 20px;">
            <h3 style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,0.6); margin: 0 0 10px 0; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 16px;">{{ $section->title }}</h3>

            @foreach($section->items->sortBy('sort_order') as $item)
                @php $c = $item->content; @endphp
                @if($section->type === 'skills')
                <div style="margin-bottom: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3px;">
                        <span style="font-size: 11px; color: rgba(255,255,255,0.9);">{{ $c['name'] ?? '' }}</span>
                    </div>
                    @php
                        $levelMap = ['beginner' => 25, 'intermediate' => 50, 'advanced' => 75, 'expert' => 100];
                        $pct = $levelMap[$c['level'] ?? 'intermediate'] ?? 50;
                    @endphp
                    <div style="height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $pct }}%; background: rgba(255,255,255,0.7); border-radius: 2px;"></div>
                    </div>
                </div>

                @elseif($section->type === 'certifications')
                <div style="margin-bottom: 8px; padding: 8px 10px; background: rgba(255,255,255,0.1); border-radius: 6px;">
                    <div style="font-size: 11px; color: white; font-weight: 600;">{{ $c['name'] ?? '' }}</div>
                    @if(!empty($c['issuer']))
                    <div style="font-size: 10px; color: rgba(255,255,255,0.6);">{{ $c['issuer'] }}</div>
                    @endif
                </div>

                @elseif($section->type === 'activities')
                <div style="margin-bottom: 8px;">
                    <div style="font-size: 11px; color: rgba(255,255,255,0.9); font-weight: 600;">{{ $c['name'] ?? '' }}</div>
                    @if(!empty($c['organization']))
                    <div style="font-size: 10px; color: rgba(255,255,255,0.6);">{{ $c['organization'] }}</div>
                    @endif
                </div>

                @elseif($section->type === 'references')
                <div style="margin-bottom: 10px;">
                    <div style="font-size: 11px; color: white; font-weight: 600;">{{ $c['name'] ?? '' }}</div>
                    @if(!empty($c['title']))
                    <div style="font-size: 10px; color: rgba(255,255,255,0.6);">{{ $c['title'] }}</div>
                    @endif
                    @if(!empty($c['email']))
                    <div style="font-size: 10px; color: rgba(255,255,255,0.6); word-break: break-all;">{{ $c['email'] }}</div>
                    @endif
                </div>
                @endif
            @endforeach
        </div>
        @endforeach
    </div>

    {{-- RIGHT MAIN CONTENT --}}
    <div style="flex: 1; padding: 36px 32px; background: white; color: #374151;">

        {{-- Objective --}}
        @if($cv->objective)
        <div style="margin-bottom: 28px; padding: 16px; background: #faf5ff; border-left: 4px solid {{ $themeColor }}; border-radius: 0 8px 8px 0;">
            <p style="font-size: 13px; color: #6b7280; line-height: 1.7; margin: 0; font-style: italic;">{{ $cv->objective }}</p>
        </div>
        @endif

        {{-- Main Sections --}}
        @foreach($mainSecs as $section)
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <div style="width: 28px; height: 28px; background: {{ $themeColor }}; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span style="color: white; font-size: 13px;">
                        @switch($section->type)
                            @case('experience') 💼 @break
                            @case('education') 🎓 @break
                            @case('projects') 🚀 @break
                            @default ✦ @break
                        @endswitch
                    </span>
                </div>
                <h2 style="font-size: 14px; font-weight: 700; color: #111827; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">{{ $section->title }}</h2>
                <div style="flex: 1; height: 1px; background: #f3f4f6;"></div>
            </div>

            @foreach($section->items->sortBy('sort_order') as $item)
                @php $c = $item->content; @endphp

                @if($section->type === 'experience')
                <div style="margin-bottom: 16px; padding-left: 12px; border-left: 2px solid #f3f4f6; position: relative;">
                    <div style="position: absolute; left: -5px; top: 6px; width: 8px; height: 8px; border-radius: 50%; background: {{ $themeColor }};"></div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <strong style="font-size: 13px; color: #111827;">{{ $c['position'] ?? '' }}</strong>
                        <span style="font-size: 11px; color: #9ca3af; background: #f9fafb; padding: 1px 8px; border-radius: 20px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                    </div>
                    <div style="font-size: 12px; color: {{ $themeColor }}; margin-top: 2px; font-weight: 600;">
                        {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                    </div>
                    @if(!empty($c['description']))
                    <p style="font-size: 12px; color: #6b7280; margin-top: 6px; line-height: 1.7;">{{ $c['description'] }}</p>
                    @endif
                </div>

                @elseif($section->type === 'education')
                <div style="margin-bottom: 14px; padding-left: 12px; border-left: 2px solid #f3f4f6; position: relative;">
                    <div style="position: absolute; left: -5px; top: 6px; width: 8px; height: 8px; border-radius: 50%; background: {{ $themeColor }};"></div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <strong style="font-size: 13px; color: #111827;">{{ $c['degree'] ?? '' }}</strong>
                        <span style="font-size: 11px; color: #9ca3af; background: #f9fafb; padding: 1px 8px; border-radius: 20px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                    </div>
                    <div style="font-size: 12px; color: {{ $themeColor }}; margin-top: 2px;">{{ $c['school'] ?? '' }}</div>
                    @if(!empty($c['gpa']))
                    <span style="font-size: 11px; color: #9ca3af;">GPA: {{ $c['gpa'] }}</span>
                    @endif
                </div>

                @elseif($section->type === 'projects')
                <div style="margin-bottom: 14px; padding: 12px 14px; background: #faf5ff; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                        <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['url']))
                        <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; text-decoration: none;">↗</a>
                        @endif
                    </div>
                    @if(!empty($c['tech']))
                    <div style="margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px;">
                        @foreach(explode(',', $c['tech']) as $tech)
                        <span style="font-size: 10px; background: {{ $themeColor }}20; color: {{ $themeColor }}; padding: 2px 7px; border-radius: 4px;">{{ trim($tech) }}</span>
                        @endforeach
                    </div>
                    @endif
                    @if(!empty($c['description']))
                    <p style="font-size: 12px; color: #6b7280; margin-top: 6px; line-height: 1.7;">{{ $c['description'] }}</p>
                    @endif
                </div>

                @else
                <p style="font-size: 12px; color: #6b7280; line-height: 1.7; margin-bottom: 6px;">{{ $c['text'] ?? '' }}</p>
                @endif
            @endforeach
        </div>
        @endforeach
    </div>
</div>
