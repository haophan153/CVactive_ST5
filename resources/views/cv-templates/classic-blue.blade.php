{{--
    Classic Blue CV Template
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal  = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#4F46E5';
    $font      = $cv->font_family ?? 'Inter';
    $sections  = $cv->sections ?? collect();
    $fullName  = $personal['full_name'] ?? 'Họ và Tên';
    $email     = $personal['email'] ?? '';
    $phone     = $personal['phone'] ?? '';
    $address   = $personal['address'] ?? '';
    $website   = $personal['website'] ?? '';
    $linkedin  = $personal['linkedin'] ?? '';
    $github    = $personal['github'] ?? '';
    $avatar    = $personal['avatar'] ?? '';
@endphp

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #1f2937;">

    {{-- HEADER --}}
    <div style="background-color: {{ $themeColor }}; padding: 32px 40px; color: white;">
        <div style="display: flex; align-items: center; gap: 24px;">
            @if($avatar)
            <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid rgba(255,255,255,0.3); flex-shrink: 0;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 4px 0; letter-spacing: -0.5px;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; opacity: 0.85; margin: 0; line-height: 1.5;">{{ Str::limit($cv->objective, 120) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info bar --}}
        <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 16px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 12px; opacity: 0.9;">
            @if($email)
            <span>✉ {{ $email }}</span>
            @endif
            @if($phone)
            <span>📱 {{ $phone }}</span>
            @endif
            @if($address)
            <span>📍 {{ $address }}</span>
            @endif
            @if($website)
            <span>🌐 {{ $website }}</span>
            @endif
            @if($linkedin)
            <span>in {{ $linkedin }}</span>
            @endif
            @if($github)
            <span>⚡ {{ $github }}</span>
            @endif
        </div>
    </div>

    {{-- BODY --}}
    <div style="padding: 28px 40px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 24px;">
                {{-- Section Title --}}
                <div style="display: flex; align-items: center; margin-bottom: 12px;">
                    <div style="width: 4px; height: 20px; background-color: {{ $themeColor }}; margin-right: 10px; border-radius: 2px;"></div>
                    <h2 style="font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #111827; margin: 0;">{{ $section->title }}</h2>
                    <div style="flex: 1; height: 1px; background-color: #e5e7eb; margin-left: 12px;"></div>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 14px; padding-left: 14px; border-left: 2px solid #f3f4f6;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #111827;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #6b7280;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 2px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #4b5563; margin-top: 6px; line-height: 1.6;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 12px; padding-left: 14px; border-left: 2px solid #f3f4f6;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #111827;">{{ $c['degree'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #6b7280;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 2px;">{{ $c['school'] ?? '' }}</div>
                        @if(!empty($c['gpa']))
                        <span style="font-size: 11px; color: #6b7280;">GPA: {{ $c['gpa'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: inline-flex; align-items: center; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 12px; margin: 3px; font-size: 12px; gap: 6px;">
                        <span style="color: #111827; font-weight: 500;">{{ $c['name'] ?? '' }}</span>
                        @if(!empty($c['level']))
                        <span style="color: {{ $themeColor }}; font-size: 10px;">
                            @switch($c['level'])
                                @case('beginner') ● ○ ○ ○ @break
                                @case('intermediate') ● ● ○ ○ @break
                                @case('advanced') ● ● ● ○ @break
                                @case('expert') ● ● ● ● @break
                            @endswitch
                        </span>
                        @endif
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 8px; padding-left: 14px; border-left: 2px solid #f3f4f6; display: flex; justify-content: space-between;">
                        <div>
                            <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 12px; color: #6b7280;"> · {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #6b7280;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding-left: 14px; border-left: 2px solid #f3f4f6;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }};">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #4b5563; margin-top: 6px; line-height: 1.6;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 8px; padding-left: 14px; border-left: 2px solid #f3f4f6;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['period']))
                            <span style="font-size: 11px; color: #6b7280;">{{ $c['period'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['organization']))
                        <div style="font-size: 12px; color: {{ $themeColor }};">{{ $c['organization'] }}</div>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 10px; padding: 12px; background: #f9fafb; border-radius: 8px;">
                        <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: {{ $themeColor }};">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else {{-- custom --}}
                    <p style="font-size: 13px; color: #4b5563; line-height: 1.6; margin-bottom: 6px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
