{{--
    Modern Dark CV Template
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#6366F1';
    $font       = $cv->font_family ?? 'Inter';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; background: #0f172a; color: #e2e8f0; min-height: 297mm;">

    {{-- HEADER --}}
    <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 40px 48px; border-bottom: 2px solid {{ $themeColor }}; position: relative; overflow: hidden;">
        {{-- Decorative circle --}}
        <div style="position: absolute; top: -60px; right: -60px; width: 200px; height: 200px; border-radius: 50%; background: {{ $themeColor }}; opacity: 0.08;"></div>
        <div style="position: absolute; bottom: -40px; left: 40%; width: 140px; height: 140px; border-radius: 50%; background: {{ $themeColor }}; opacity: 0.05;"></div>

        <div style="display: flex; align-items: center; gap: 28px; position: relative; z-index: 1;">
            @if($avatar)
            <div style="width: 88px; height: 88px; border-radius: 50%; overflow: hidden; border: 3px solid {{ $themeColor }}; flex-shrink: 0; box-shadow: 0 0 20px {{ $themeColor }}40;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 30px; font-weight: 800; margin: 0 0 6px 0; letter-spacing: -0.5px; color: #f8fafc;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; color: #94a3b8; margin: 0; line-height: 1.6; max-width: 480px;">{{ Str::limit($cv->objective, 140) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact bar --}}
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #1e293b; font-size: 12px; position: relative; z-index: 1;">
            @if($email)
            <span style="display: flex; align-items: center; gap: 6px; color: #94a3b8;">
                <span style="color: {{ $themeColor }}; font-size: 14px;">✉</span> {{ $email }}
            </span>
            @endif
            @if($phone)
            <span style="display: flex; align-items: center; gap: 6px; color: #94a3b8;">
                <span style="color: {{ $themeColor }}; font-size: 14px;">📱</span> {{ $phone }}
            </span>
            @endif
            @if($address)
            <span style="display: flex; align-items: center; gap: 6px; color: #94a3b8;">
                <span style="color: {{ $themeColor }}; font-size: 14px;">📍</span> {{ $address }}
            </span>
            @endif
            @if($website)
            <span style="display: flex; align-items: center; gap: 6px; color: #94a3b8;">
                <span style="color: {{ $themeColor }}; font-size: 14px;">🌐</span> {{ $website }}
            </span>
            @endif
            @if($linkedin)
            <span style="display: flex; align-items: center; gap: 6px; color: #94a3b8;">
                <span style="color: {{ $themeColor }}; font-weight: 700; font-size: 11px;">in</span> {{ $linkedin }}
            </span>
            @endif
            @if($github)
            <span style="display: flex; align-items: center; gap: 6px; color: #94a3b8;">
                <span style="color: {{ $themeColor }}; font-size: 14px;">⚡</span> {{ $github }}
            </span>
            @endif
        </div>
    </div>

    {{-- BODY --}}
    <div style="padding: 32px 48px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 28px;">
                {{-- Section Title --}}
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                    <div style="width: 3px; height: 18px; background: {{ $themeColor }}; border-radius: 2px; box-shadow: 0 0 8px {{ $themeColor }}60;"></div>
                    <h2 style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2.5px; color: {{ $themeColor }}; margin: 0;">{{ $section->title }}</h2>
                    <div style="flex: 1; height: 1px; background: linear-gradient(to right, #1e293b, transparent);"></div>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 16px; padding: 14px 16px; background: #1e293b; border-radius: 10px; border-left: 3px solid {{ $themeColor }}40; hover: border-left-color: {{ $themeColor }};">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <strong style="font-size: 14px; color: #f1f5f9;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #64748b; background: #0f172a; padding: 2px 8px; border-radius: 20px; white-space: nowrap; margin-left: 8px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 4px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #94a3b8; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 14px; padding: 14px 16px; background: #1e293b; border-radius: 10px; border-left: 3px solid {{ $themeColor }}40;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <strong style="font-size: 14px; color: #f1f5f9;">{{ $c['degree'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #64748b; background: #0f172a; padding: 2px 8px; border-radius: 20px; white-space: nowrap; margin-left: 8px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 4px;">{{ $c['school'] ?? '' }}</div>
                        @if(!empty($c['gpa']))
                        <span style="font-size: 11px; color: #64748b; margin-top: 4px; display: inline-block;">GPA: {{ $c['gpa'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'skills')
                    <span style="display: inline-flex; align-items: center; gap: 8px; background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 5px 14px; margin: 3px; font-size: 12px; color: #e2e8f0;">
                        <span>{{ $c['name'] ?? '' }}</span>
                        @if(!empty($c['level']))
                        <span style="color: {{ $themeColor }}; font-size: 10px; letter-spacing: 1px;">
                            @switch($c['level'])
                                @case('beginner') ▮ ▯ ▯ ▯ @break
                                @case('intermediate') ▮ ▮ ▯ ▯ @break
                                @case('advanced') ▮ ▮ ▮ ▯ @break
                                @case('expert') ▮ ▮ ▮ ▮ @break
                            @endswitch
                        </span>
                        @endif
                    </span>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding: 12px 16px; background: #1e293b; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 13px; color: #f1f5f9;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 11px; color: #64748b;"> · {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: {{ $themeColor }}; background: {{ $themeColor }}20; padding: 2px 8px; border-radius: 20px;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 16px; padding: 14px 16px; background: #1e293b; border-radius: 10px; border-left: 3px solid {{ $themeColor }}40;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <strong style="font-size: 14px; color: #f1f5f9;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; text-decoration: none;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: {{ $themeColor }}; margin-top: 6px; background: {{ $themeColor }}15; display: inline-block; padding: 2px 10px; border-radius: 4px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #94a3b8; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding: 12px 16px; background: #1e293b; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 13px; color: #f1f5f9;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['organization']))
                            <div style="font-size: 12px; color: {{ $themeColor }}; margin-top: 2px;">{{ $c['organization'] }}</div>
                            @endif
                        </div>
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #64748b;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 14px 16px; background: #1e293b; border-radius: 10px; border-top: 2px solid {{ $themeColor }}40;">
                        <strong style="font-size: 13px; color: #f1f5f9;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: {{ $themeColor }}; margin-top: 2px;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #94a3b8; line-height: 1.7; padding-left: 12px; border-left: 2px solid #1e293b; margin-bottom: 6px;">{{ $c['text'] ?? '' }}</p>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
</div>
