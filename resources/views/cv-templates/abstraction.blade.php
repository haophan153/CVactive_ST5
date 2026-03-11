{{--
    Abstraction CV Template - Creative colorful design with geometric elements
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#6366F1';
    $accentColor = $cv->theme_color ?? '#8B5CF6';
    $font       = $cv->font_family ?? 'Poppins';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #1F2937; background: #FFFFFF;">
    
    {{-- HEADER with geometric background --}}
    <div style="background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $accentColor }} 100%); padding: 36px 40px; position: relative; overflow: hidden;">
        {{-- Geometric shapes --}}
        <div style="position: absolute; top: -30px; right: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -50px; left: 20%; width: 100px; height: 100px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
        
        <div style="display: flex; align-items: center; gap: 24px; position: relative; z-index: 1;">
            @if($avatar)
            <div style="width: 100px; height: 100px; border-radius: 12px; overflow: hidden; border: 4px solid rgba(255,255,255,0.4); flex-shrink: 0; box-shadow: 0 8px 32px rgba(0,0,0,0.15);">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1; color: white;">
                <h1 style="font-size: 28px; font-weight: 700; margin: 0 0 8px 0; letter-spacing: -0.5px;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; opacity: 0.9; margin: 0; line-height: 1.6; max-width: 500px;">{{ Str::limit($cv->objective, 120) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info --}}
        <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 20px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 12px; color: rgba(255,255,255,0.9); position: relative; z-index: 1;">
            @if($email)
            <span style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px;">
                <span>✉</span> {{ $email }}
            </span>
            @endif
            @if($phone)
            <span style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px;">
                <span>📱</span> {{ $phone }}
            </span>
            @endif
            @if($address)
            <span style="display: flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px;">
                <span>📍</span> {{ $address }}
            </span>
            @endif
            @if($linkedin)
            <span style="background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px;">in {{ $linkedin }}</span>
            @endif
            @if($github)
            <span style="background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px;">⚡ {{ $github }}</span>
            @endif
        </div>
    </div>

    {{-- BODY --}}
    <div style="padding: 28px 40px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 24px;">
                {{-- Section Title with geometric accent --}}
                <div style="display: flex; align-items: center; margin-bottom: 14px;">
                    <div style="width: 12px; height: 12px; background: {{ $themeColor }}; transform: rotate(45deg); margin-right: 12px;"></div>
                    <h2 style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #111827; margin: 0;">{{ $section->title }}</h2>
                    <div style="flex: 1; height: 2px; background: linear-gradient(90deg, {{ $themeColor }} 0%, transparent 100%); margin-left: 12px;"></div>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 14px; padding: 14px; background: #F9FAFB; border-radius: 10px; border-left: 4px solid {{ $themeColor }};">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #111827;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #6B7280; background: {{ $accentColor }}; color: white; padding: 2px 10px; border-radius: 12px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 13px; color: {{ $themeColor }}; font-weight: 600; margin-top: 4px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #4B5563; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 12px; padding: 12px; background: #F9FAFB; border-radius: 8px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 8px; height: 8px; background: {{ $accentColor }}; border-radius: 50%;"></div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <strong style="font-size: 14px; color: #111827;">{{ $c['degree'] ?? '' }}</strong>
                                <span style="font-size: 11px; color: #6B7280;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                            </div>
                            <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 2px;">{{ $c['school'] ?? '' }}</div>
                        </div>
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 12px; color: white; background: linear-gradient(135deg, {{ $themeColor }} 0%, {{ $accentColor }} 100%); padding: 6px 16px; border-radius: 20px; font-weight: 500;">
                                {{ $s['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding: 12px; background: #F9FAFB; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 12px; color: #6B7280;"> · {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: {{ $themeColor }}; font-weight: 600;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding: 14px; background: #F9FAFB; border-radius: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; text-decoration: none;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: {{ $accentColor }}; margin-top: 4px; font-weight: 600;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #4B5563; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding: 12px; background: #F9FAFB; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['organization']))
                            <div style="font-size: 12px; color: {{ $themeColor }};">{{ $c['organization'] }}</div>
                            @endif
                        </div>
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #6B7280;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 14px; background: linear-gradient(135deg, {{ $themeColor }}10, {{ $accentColor }}10); border-radius: 10px; border: 1px solid {{ $themeColor }}30;">
                        <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #6B7280; margin-top: 4px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #4B5563; line-height: 1.7; margin-bottom: 8px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
