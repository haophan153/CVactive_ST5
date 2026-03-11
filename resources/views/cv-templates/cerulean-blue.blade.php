{{--
    Cerulean Blue CV Template - Professional blue design
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#0284C7';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #1E293B; background: #F0F9FF;">
    
    {{-- HEADER with blue accent --}}
    <div style="background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%); padding: 32px 40px;">
        <div style="display: flex; align-items: center; gap: 24px;">
            @if($avatar)
            <div style="width: 85px; height: 85px; border-radius: 8px; overflow: hidden; border: 3px solid rgba(255,255,255,0.4); flex-shrink: 0;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1; color: white;">
                <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 6px 0; letter-spacing: -0.5px;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; opacity: 0.9; margin: 0; line-height: 1.5; max-width: 500px;">{{ Str::limit($cv->objective, 120) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info --}}
        <div style="display: flex; flex-wrap: wrap; gap: 14px; margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 12px; color: rgba(255,255,255,0.95);">
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
    <div style="padding: 28px 40px; background: white;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 22px;">
                {{-- Section Title with blue accent --}}
                <div style="display: flex; align-items: center; margin-bottom: 14px;">
                    <div style="width: 4px; height: 22px; background: #0284C7; margin-right: 10px; border-radius: 2px;"></div>
                    <h2 style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0F172A; margin: 0;">{{ $section->title }}</h2>
                    <div style="flex: 1; height: 1px; background: #E2E8F0; margin-left: 12px;"></div>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 14px; padding: 14px; background: #F0F9FF; border-radius: 8px; border-left: 3px solid #0284C7;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1E293B;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #64748B;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 13px; color: #0284C7; font-weight: 600; margin-top: 3px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #475569; margin-top: 8px; line-height: 1.6;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 12px; padding-left: 14px; border-left: 2px solid #E2E8F0;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1E293B;">{{ $c['degree'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #64748B;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12px; color: #0284C7; font-weight: 600; margin-top: 3px;">{{ $c['school'] ?? '' }}</div>
                        @if(!empty($c['gpa']))
                        <span style="font-size: 11px; color: #64748B;">GPA: {{ $c['gpa'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 12px; color: #0284C7; background: #F0F9FF; border: 1px solid #BAE6FD; padding: 5px 12px; border-radius: 4px; font-weight: 500;">
                                {{ $s['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding-left: 14px; border-left: 2px solid #E2E8F0; display: flex; justify-content: space-between;">
                        <div>
                            <strong style="font-size: 13px; color: #1E293B;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 12px; color: #64748B;"> · {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #64748B;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding: 14px; background: #F0F9FF; border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1E293B;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: #0284C7;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #64748B; margin-top: 3px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #475569; margin-top: 8px; line-height: 1.6;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding-left: 14px; border-left: 2px solid #E2E8F0;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong style="font-size: 13px; color: #1E293B;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['period']))
                            <span style="font-size: 11px; color: #64748B;">{{ $c['period'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['organization']))
                        <div style="font-size: 12px; color: #0284C7;">{{ $c['organization'] }}</div>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 12px; background: #F0F9FF; border-radius: 8px;">
                        <strong style="font-size: 13px; color: #1E293B;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: #0284C7; font-weight: 600; margin-top: 2px;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #64748B; margin-top: 2px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #475569; line-height: 1.6; margin-bottom: 6px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
