{{--
    Minimal White CV Template
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#111827';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #374151; background: #ffffff; min-height: 297mm;">

    {{-- HEADER --}}
    <div style="padding: 48px 56px 32px; border-bottom: 1px solid #f3f4f6;">
        <div style="display: flex; align-items: flex-start; gap: 28px;">
            @if($avatar)
            <div style="width: 72px; height: 72px; border-radius: 4px; overflow: hidden; flex-shrink: 0; border: 1px solid #e5e7eb;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 32px; font-weight: 300; letter-spacing: -1px; color: #111827; margin: 0 0 8px 0; line-height: 1.1;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; color: #6b7280; margin: 0; line-height: 1.7; max-width: 500px; font-weight: 300;">{{ Str::limit($cv->objective, 180) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact row --}}
        <div style="display: flex; flex-wrap: wrap; gap: 0; margin-top: 20px; font-size: 11.5px; color: #9ca3af; border-top: 1px solid #f3f4f6; padding-top: 16px;">
            @if($email)
            <span style="margin-right: 24px; display: flex; align-items: center; gap: 5px;">
                <span style="color: {{ $themeColor }}; opacity: 0.5; font-size: 10px;">✉</span> {{ $email }}
            </span>
            @endif
            @if($phone)
            <span style="margin-right: 24px; display: flex; align-items: center; gap: 5px;">
                <span style="color: {{ $themeColor }}; opacity: 0.5; font-size: 10px;">📞</span> {{ $phone }}
            </span>
            @endif
            @if($address)
            <span style="margin-right: 24px; display: flex; align-items: center; gap: 5px;">
                <span style="color: {{ $themeColor }}; opacity: 0.5; font-size: 10px;">📍</span> {{ $address }}
            </span>
            @endif
            @if($website)
            <span style="margin-right: 24px;">{{ $website }}</span>
            @endif
            @if($linkedin)
            <span style="margin-right: 24px;">{{ $linkedin }}</span>
            @endif
            @if($github)
            <span>{{ $github }}</span>
            @endif
        </div>
    </div>

    {{-- BODY --}}
    <div style="padding: 32px 56px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 28px; display: flex; gap: 24px;">
                {{-- Left label --}}
                <div style="width: 120px; flex-shrink: 0; padding-top: 2px;">
                    <h2 style="font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: {{ $themeColor }}; margin: 0; opacity: 0.6;">{{ $section->title }}</h2>
                </div>

                {{-- Right content --}}
                <div style="flex: 1; border-left: 1px solid #f3f4f6; padding-left: 24px;">
                    @foreach($section->items->sortBy('sort_order') as $item)
                        @php $c = $item->content; @endphp

                        @if($section->type === 'experience')
                        <div style="margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <strong style="font-size: 14px; color: #111827; font-weight: 600;">{{ $c['position'] ?? '' }}</strong>
                                <span style="font-size: 11px; color: #9ca3af; white-space: nowrap; margin-left: 8px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                            </div>
                            <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">
                                {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ', ' : '' }}{{ $c['location'] ?? '' }}
                            </div>
                            @if(!empty($c['description']))
                            <p style="font-size: 12px; color: #6b7280; margin-top: 6px; line-height: 1.8; font-weight: 300;">{{ $c['description'] }}</p>
                            @endif
                        </div>

                        @elseif($section->type === 'education')
                        <div style="margin-bottom: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <strong style="font-size: 14px; color: #111827; font-weight: 600;">{{ $c['degree'] ?? '' }}</strong>
                                <span style="font-size: 11px; color: #9ca3af; white-space: nowrap; margin-left: 8px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' – ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                            </div>
                            <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">{{ $c['school'] ?? '' }}</div>
                            @if(!empty($c['gpa']))
                            <span style="font-size: 11px; color: #9ca3af;">GPA: {{ $c['gpa'] }}</span>
                            @endif
                        </div>

                        @elseif($section->type === 'skills')
                        <span style="display: inline-block; font-size: 12px; color: #374151; margin: 2px 4px 2px 0; padding-bottom: 1px; border-bottom: 1.5px solid {{ $themeColor }}; opacity: {{ $c['level'] === 'expert' ? '1' : ($c['level'] === 'advanced' ? '0.8' : ($c['level'] === 'intermediate' ? '0.6' : '0.4')) }};">
                            {{ $c['name'] ?? '' }}
                        </span>

                        @elseif($section->type === 'certifications')
                        <div style="margin-bottom: 8px; display: flex; justify-content: space-between; align-items: baseline;">
                            <div>
                                <span style="font-size: 13px; color: #111827; font-weight: 500;">{{ $c['name'] ?? '' }}</span>
                                @if(!empty($c['issuer']))
                                <span style="font-size: 11px; color: #9ca3af;"> · {{ $c['issuer'] }}</span>
                                @endif
                            </div>
                            @if(!empty($c['date']))
                            <span style="font-size: 11px; color: #9ca3af;">{{ $c['date'] }}</span>
                            @endif
                        </div>

                        @elseif($section->type === 'projects')
                        <div style="margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <strong style="font-size: 13px; color: #111827; font-weight: 600;">{{ $c['name'] ?? '' }}</strong>
                                @if(!empty($c['url']))
                                <a href="{{ $c['url'] }}" style="font-size: 11px; color: #9ca3af; text-decoration: none;">↗ {{ $c['url'] }}</a>
                                @endif
                            </div>
                            @if(!empty($c['tech']))
                            <div style="font-size: 11px; color: {{ $themeColor }}; opacity: 0.5; margin-top: 2px; font-style: italic;">{{ $c['tech'] }}</div>
                            @endif
                            @if(!empty($c['description']))
                            <p style="font-size: 12px; color: #6b7280; margin-top: 6px; line-height: 1.8; font-weight: 300;">{{ $c['description'] }}</p>
                            @endif
                        </div>

                        @elseif($section->type === 'activities')
                        <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                            <div>
                                <span style="font-size: 13px; color: #111827; font-weight: 500;">{{ $c['name'] ?? '' }}</span>
                                @if(!empty($c['organization']))
                                <div style="font-size: 11px; color: #9ca3af; margin-top: 1px;">{{ $c['organization'] }}</div>
                                @endif
                            </div>
                            @if(!empty($c['period']))
                            <span style="font-size: 11px; color: #9ca3af;">{{ $c['period'] }}</span>
                            @endif
                        </div>

                        @elseif($section->type === 'references')
                        <div style="margin-bottom: 12px;">
                            <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['title']))
                            <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">{{ $c['title'] }}</div>
                            @endif
                            <div style="font-size: 11px; color: #9ca3af; margin-top: 2px;">
                                {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                            </div>
                        </div>

                        @else
                        <p style="font-size: 13px; color: #6b7280; line-height: 1.8; font-weight: 300; margin-bottom: 6px;">{{ $c['text'] ?? '' }}</p>
                        @endif

                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
