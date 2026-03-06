{{--
    Executive Pro CV Template – Classic, prestigious, elegant
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#1E3A5F';
    $font       = $cv->font_family ?? 'Open Sans';
    $sections   = $cv->sections ?? collect();
    $fullName   = $personal['full_name'] ?? 'Họ và Tên';
    $email      = $personal['email'] ?? '';
    $phone      = $personal['phone'] ?? '';
    $address    = $personal['address'] ?? '';
    $website    = $personal['website'] ?? '';
    $linkedin   = $personal['linkedin'] ?? '';
    $github     = $personal['github'] ?? '';
    $avatar     = $personal['avatar'] ?? '';
@endphp

<div class="cv-document" style="font-family: '{{ $font }}', serif; background: #ffffff; color: #1e293b; min-height: 297mm;">

    {{-- HEADER --}}
    <div style="padding: 0; background: {{ $themeColor }};">
        {{-- Gold accent line --}}
        <div style="height: 4px; background: linear-gradient(to right, #c9a227, #f0d060, #c9a227);"></div>

        <div style="padding: 32px 48px; display: flex; align-items: center; gap: 28px;">
            @if($avatar)
            <div style="width: 90px; height: 90px; border-radius: 50%; overflow: hidden; border: 3px solid rgba(201,162,39,0.6); flex-shrink: 0;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 28px; font-weight: 700; color: #ffffff; margin: 0 0 4px 0; letter-spacing: 0.5px; text-transform: uppercase; font-variant: small-caps;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 12.5px; color: rgba(255,255,255,0.75); margin: 0; line-height: 1.6; max-width: 500px; font-style: italic;">{{ Str::limit($cv->objective, 150) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact bar with gold accent --}}
        <div style="background: rgba(0,0,0,0.25); padding: 12px 48px; display: flex; flex-wrap: wrap; gap: 20px; border-top: 1px solid rgba(201,162,39,0.3);">
            @if($email)
            <span style="font-size: 11.5px; color: rgba(255,255,255,0.8); display: flex; align-items: center; gap: 6px;">
                <span style="color: #c9a227;">✉</span> {{ $email }}
            </span>
            @endif
            @if($phone)
            <span style="font-size: 11.5px; color: rgba(255,255,255,0.8); display: flex; align-items: center; gap: 6px;">
                <span style="color: #c9a227;">☎</span> {{ $phone }}
            </span>
            @endif
            @if($address)
            <span style="font-size: 11.5px; color: rgba(255,255,255,0.8); display: flex; align-items: center; gap: 6px;">
                <span style="color: #c9a227;">◈</span> {{ $address }}
            </span>
            @endif
            @if($website)
            <span style="font-size: 11.5px; color: rgba(255,255,255,0.8);">{{ $website }}</span>
            @endif
            @if($linkedin)
            <span style="font-size: 11.5px; color: rgba(255,255,255,0.8);">{{ $linkedin }}</span>
            @endif
            @if($github)
            <span style="font-size: 11.5px; color: rgba(255,255,255,0.8);">{{ $github }}</span>
            @endif
        </div>
        <div style="height: 4px; background: linear-gradient(to right, #c9a227, #f0d060, #c9a227);"></div>
    </div>

    {{-- BODY --}}
    <div style="padding: 32px 48px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 26px;">
                {{-- Section Title --}}
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <h2 style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: {{ $themeColor }}; margin: 0; white-space: nowrap;">{{ $section->title }}</h2>
                    <div style="flex: 1; height: 2px; background: linear-gradient(to right, {{ $themeColor }}, #c9a22740, transparent);"></div>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 16px; padding: 0 0 16px 16px; border-left: 2px solid #e2e8f0; position: relative;">
                        <div style="position: absolute; left: -5px; top: 6px; width: 8px; height: 8px; border-radius: 50%; background: #c9a227; border: 2px solid white; box-shadow: 0 0 0 1px {{ $themeColor }};"></div>
                        <div style="display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 4px;">
                            <strong style="font-size: 14px; color: #1e293b;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #64748b; font-style: italic;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12.5px; color: {{ $themeColor }}; font-weight: 600; margin-top: 3px;">
                            {{ $c['company'] ?? '' }}@if(($c['company'] ?? '') && ($c['location'] ?? '')) <span style="font-weight: 400; color: #94a3b8;"> · {{ $c['location'] ?? '' }}</span>@endif
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #475569; margin-top: 6px; line-height: 1.75; text-align: justify;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 14px; padding: 0 0 14px 16px; border-left: 2px solid #e2e8f0; position: relative;">
                        <div style="position: absolute; left: -5px; top: 6px; width: 8px; height: 8px; border-radius: 50%; background: #c9a227; border: 2px solid white; box-shadow: 0 0 0 1px {{ $themeColor }};"></div>
                        <div style="display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 4px;">
                            <strong style="font-size: 14px; color: #1e293b;">{{ $c['degree'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #64748b; font-style: italic;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12.5px; color: {{ $themeColor }}; font-weight: 600; margin-top: 3px;">{{ $c['school'] ?? '' }}</div>
                        @if(!empty($c['gpa']))
                        <span style="font-size: 11px; color: #94a3b8; margin-top: 2px; display: inline-block;">GPA: {{ $c['gpa'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 6px;">
                        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 4px 14px 4px 12px; border: 1px solid #e2e8f0; border-radius: 2px; background: #f8fafc;">
                            <span style="font-size: 12.5px; color: #1e293b;">{{ $c['name'] ?? '' }}</span>
                            @if(!empty($c['level']))
                            @php
                                $dots = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3, 'expert' => 4];
                                $count = $dots[$c['level']] ?? 2;
                            @endphp
                            <span style="display: flex; gap: 2px;">
                                @for($i = 0; $i < 4; $i++)
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $i < $count ? '#c9a227' : '#e2e8f0' }};"></span>
                                @endfor
                            </span>
                            @endif
                        </div>
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding: 10px 0; border-bottom: 1px solid #f8fafc; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 13px; color: #1e293b;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 11.5px; color: #94a3b8;"> · {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #64748b; font-style: italic;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding-left: 16px; border-left: 2px solid #e2e8f0;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 13px; color: #1e293b;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; text-decoration: none;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px; font-style: italic;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #475569; margin-top: 6px; line-height: 1.75; text-align: justify;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding: 0 0 10px 16px; border-left: 2px solid #e2e8f0; display: flex; justify-content: space-between;">
                        <div>
                            <strong style="font-size: 13px; color: #1e293b;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['organization']))
                            <div style="font-size: 11.5px; color: {{ $themeColor }}; margin-top: 2px;">{{ $c['organization'] }}</div>
                            @endif
                        </div>
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #64748b; font-style: italic;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 12px 16px; background: #f8f9fb; border-radius: 4px; border-left: 3px solid #c9a227;">
                        <strong style="font-size: 13px; color: #1e293b;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: {{ $themeColor }}; margin-top: 2px;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 3px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 12.5px; color: #475569; line-height: 1.75; margin-bottom: 6px; text-align: justify;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>

    {{-- Footer --}}
    <div style="height: 6px; background: linear-gradient(to right, #c9a227, #f0d060, #c9a227); margin-top: auto;"></div>
</div>
