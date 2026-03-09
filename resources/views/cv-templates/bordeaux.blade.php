{{--
    Bordeaux CV Template - Elegant wine/red design
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#7F1D1D';
    $font       = $cv->font_family ?? 'Cormorant Garamond';
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

<div class="cv-document" style="font-family: '{{ $font }}', serif; color: #1C1917; background: #FFF7ED;">
    
    {{-- HEADER with elegant border --}}
    <div style="background: linear-gradient(135deg, #7F1D1D 0%, #991B1B 100%); padding: 36px 40px; position: relative;">
        {{-- Corner accents --}}
        <div style="position: absolute; top: 0; left: 0; width: 60px; height: 60px; border-top: 3px solid rgba(255,255,255,0.5); border-left: 3px solid rgba(255,255,255,0.5);"></div>
        <div style="position: absolute; bottom: 0; right: 0; width: 60px; height: 60px; border-bottom: 3px solid rgba(255,255,255,0.5); border-right: 3px solid rgba(255,255,255,0.5);"></div>
        
        <div style="display: flex; align-items: center; gap: 24px;">
            @if($avatar)
            <div style="width: 85px; height: 85px; border-radius: 50%; overflow: hidden; border: 3px solid rgba(255,255,255,0.5); flex-shrink: 0;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1; color: white;">
                <h1 style="font-size: 26px; font-weight: 600; letter-spacing: 1px; margin: 0 0 8px 0; text-transform: uppercase;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 12px; opacity: 0.9; margin: 0; line-height: 1.6; max-width: 480px; font-style: italic;">{{ Str::limit($cv->objective, 130) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info --}}
        <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 11px; color: rgba(255,255,255,0.9);">
            @if($email)
            <span>✉ {{ $email }}</span>
            @endif
            @if($phone)
            <span>☎ {{ $phone }}</span>
            @endif
            @if($address)
            <span>⌖ {{ $address }}</span>
            @endif
            @if($website)
            <span>{{ $website }}</span>
            @endif
            @if($linkedin)
            <span>LinkedIn: {{ $linkedin }}</span>
            @endif
            @if($github)
            <span>GitHub: {{ $github }}</span>
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
                <div style="margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #7F1D1D; display: inline-block;">
                    <h2 style="font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 2px; color: #7F1D1D; margin: 0;">{{ $section->title }}</h2>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-left: 3px solid #7F1D1D;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1C1917;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #78716C; font-style: italic;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 13px; color: #7F1D1D; font-weight: 600; margin-top: 3px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' | ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #44403C; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 12px; padding: 12px; background: white; text-align: center;">
                        <strong style="font-size: 14px; color: #1C1917;">{{ $c['degree'] ?? '' }}</strong>
                        <div style="font-size: 12px; color: #7F1D1D; font-weight: 600; margin-top: 3px;">{{ $c['school'] ?? '' }}</div>
                        <span style="font-size: 11px; color: #78716C; font-style: italic;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 12px; color: white; background: #7F1D1D; padding: 5px 14px; border-radius: 2px;">
                                {{ $s['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding: 10px; background: white; border-left: 3px solid #7F1D1D;">
                        <strong style="font-size: 13px; color: #1C1917;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['issuer']))
                        <span style="font-size: 11px; color: #57534E;"> - {{ $c['issuer'] }}</span>
                        @endif
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #78716C; margin-left: 10px;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-left: 3px solid #7F1D1D;">
                        <strong style="font-size: 14px; color: #1C1917;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['url']))
                        <a href="{{ $c['url'] }}" style="font-size: 11px; color: #7F1D1D; margin-left: 8px;">{{ $c['url'] }}</a>
                        @endif
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #78716C; margin-top: 4px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #44403C; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding: 10px; background: white; border-left: 3px solid #7F1D1D;">
                        <strong style="font-size: 13px; color: #1C1917;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['organization']))
                        <div style="font-size: 12px; color: #7F1D1D;">{{ $c['organization'] }}</div>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 12px; background: white; border: 1px solid #E7E5E4; text-align: center;">
                        <strong style="font-size: 13px; color: #1C1917;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: #7F1D1D; font-weight: 600;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #57534E; margin-top: 4px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' | ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #44403C; line-height: 1.7; margin-bottom: 6px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
