{{--
    Tech Engineer CV Template – Terminal/Code inspired
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#10B981';
    $font       = $cv->font_family ?? 'Roboto';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; background: #f8fafc; color: #1e293b; min-height: 297mm;">

    {{-- HEADER --}}
    <div style="background: #1e293b; padding: 32px 44px; position: relative; overflow: hidden;">
        {{-- Grid pattern overlay --}}
        <div style="position: absolute; inset: 0; opacity: 0.04; background-image: linear-gradient({{ $themeColor }} 1px, transparent 1px), linear-gradient(90deg, {{ $themeColor }} 1px, transparent 1px); background-size: 28px 28px;"></div>

        <div style="display: flex; align-items: center; gap: 24px; position: relative; z-index: 1;">
            @if($avatar)
            <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; border: 2px solid {{ $themeColor }}; flex-shrink: 0;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <div style="font-size: 11px; color: {{ $themeColor }}; font-family: monospace; margin-bottom: 4px;">$ whoami</div>
                <h1 style="font-size: 26px; font-weight: 700; color: #f8fafc; margin: 0 0 6px 0; letter-spacing: -0.3px;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 12px; color: #94a3b8; margin: 0; line-height: 1.6; max-width: 460px; font-family: monospace;">// {{ Str::limit($cv->objective, 130) }}</p>
                @endif
            </div>
        </div>

        {{-- Terminal-style contact bar --}}
        <div style="margin-top: 20px; padding: 10px 16px; background: #0f172a; border-radius: 6px; font-family: monospace; font-size: 11px; color: #64748b; display: flex; flex-wrap: wrap; gap: 16px; position: relative; z-index: 1;">
            <span style="color: {{ $themeColor }};">~/</span>
            @if($email)<span><span style="color: {{ $themeColor }};">email:</span> {{ $email }}</span>@endif
            @if($phone)<span><span style="color: {{ $themeColor }};">tel:</span> {{ $phone }}</span>@endif
            @if($address)<span><span style="color: {{ $themeColor }};">loc:</span> {{ $address }}</span>@endif
            @if($github)<span><span style="color: {{ $themeColor }};">github:</span> {{ $github }}</span>@endif
            @if($linkedin)<span><span style="color: {{ $themeColor }};">linkedin:</span> {{ $linkedin }}</span>@endif
            @if($website)<span><span style="color: {{ $themeColor }};">web:</span> {{ $website }}</span>@endif
        </div>
    </div>

    {{-- BODY --}}
    <div style="padding: 28px 44px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 28px;">
                {{-- Section Title --}}
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <span style="font-family: monospace; font-size: 12px; color: {{ $themeColor }}; font-weight: 700;">►</span>
                    <h2 style="font-size: 13px; font-weight: 700; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 1.5px;">{{ $section->title }}</h2>
                    <div style="flex: 1; border-bottom: 1px dashed #e2e8f0;"></div>
                </div>

                {{-- Skills: tag cloud + bars --}}
                @if($section->type === 'skills')
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    @foreach($section->items->sortBy('sort_order') as $item)
                        @php $c = $item->content;
                            $levelColors = ['expert' => $themeColor, 'advanced' => '#3b82f6', 'intermediate' => '#f59e0b', 'beginner' => '#9ca3af'];
                            $badgeColor = $levelColors[$c['level'] ?? 'intermediate'] ?? '#9ca3af';
                        @endphp
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 4px; font-family: monospace; font-size: 12px; background: white; border: 1.5px solid {{ $badgeColor }}40; color: #1e293b;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $badgeColor }}; flex-shrink: 0;"></span>
                            {{ $c['name'] ?? '' }}
                        </span>
                    @endforeach
                </div>

                @else
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 16px; background: white; border-radius: 8px; padding: 14px 16px; border: 1px solid #e2e8f0; border-top: 3px solid {{ $themeColor }};">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 6px;">
                            <strong style="font-size: 14px; color: #1e293b;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #64748b; font-family: monospace; background: #f8fafc; padding: 2px 8px; border-radius: 4px; border: 1px solid #e2e8f0;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' → ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 4px; font-family: monospace;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' @ ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #475569; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 14px; background: white; border-radius: 8px; padding: 14px 16px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 8px;">
                        <div style="flex: 1;">
                            <strong style="font-size: 13px; color: #1e293b;">{{ $c['degree'] ?? '' }}</strong>
                            <div style="font-size: 12px; color: {{ $themeColor }}; margin-top: 3px; font-family: monospace;">{{ $c['school'] ?? '' }}</div>
                            @if(!empty($c['gpa']))
                            <span style="font-size: 11px; color: #64748b; margin-top: 2px; display: inline-block;">GPA: {{ $c['gpa'] }}</span>
                            @endif
                        </div>
                        <span style="font-size: 11px; color: #64748b; font-family: monospace; background: #f8fafc; padding: 2px 10px; border-radius: 4px; border: 1px solid #e2e8f0; white-space: nowrap;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' → ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; background: white; border-radius: 8px; padding: 14px 16px; border: 1px solid #e2e8f0;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 8px; flex-wrap: wrap;">
                            <strong style="font-size: 13px; color: #1e293b; font-family: monospace;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; font-family: monospace; text-decoration: none;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="margin-top: 6px; display: flex; flex-wrap: wrap; gap: 4px;">
                            @foreach(explode(',', $c['tech']) as $tech)
                            <span style="font-size: 10px; font-family: monospace; background: {{ $themeColor }}15; color: {{ $themeColor }}; padding: 2px 7px; border-radius: 3px; border: 1px solid {{ $themeColor }}30;">{{ trim($tech) }}</span>
                            @endforeach
                        </div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #475569; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 8px; background: white; border-radius: 6px; padding: 10px 14px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-size: 12px; color: #1e293b; font-weight: 600;">{{ $c['name'] ?? '' }}</span>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 11px; color: #94a3b8;"> · {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 10px; font-family: monospace; color: {{ $themeColor }}; background: {{ $themeColor }}15; padding: 2px 8px; border-radius: 4px;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 8px; padding: 8px 0; border-bottom: 1px dashed #f1f5f9; display: flex; justify-content: space-between;">
                        <div>
                            <span style="font-size: 12px; color: #1e293b;">{{ $c['name'] ?? '' }}</span>
                            @if(!empty($c['organization']))
                            <div style="font-size: 11px; color: #94a3b8;">{{ $c['organization'] }}</div>
                            @endif
                        </div>
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #64748b; font-family: monospace;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 10px; padding: 10px 14px; background: white; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <strong style="font-size: 12px; color: #1e293b;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 11px; color: {{ $themeColor }}; font-family: monospace; margin-top: 2px;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 3px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 12px; color: #475569; line-height: 1.7; padding: 8px 12px; background: white; border-left: 3px solid {{ $themeColor }}; border-radius: 0 6px 6px 0; margin-bottom: 6px; font-family: monospace;">{{ $c['text'] ?? '' }}</p>
                    @endif
                @endforeach
                @endif
            </div>
        @endforeach
    </div>
</div>
