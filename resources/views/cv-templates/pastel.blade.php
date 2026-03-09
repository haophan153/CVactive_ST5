{{--
    Pastel CV Template - Soft pastel colors
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#F472B6';
    $accentColor = $cv->theme_color ?? '#A78BFA';
    $font       = $cv->font_family ?? 'Nunito';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #374151; background: #FEFCE8;">
    
    {{-- HEADER with pastel gradient --}}
    <div style="background: linear-gradient(135deg, #FCE7F3 0%, #EDE9FE 50%, #FEF3C7 100%); padding: 32px 40px; position: relative;">
        {{-- Decorative dots --}}
        <div style="position: absolute; top: 10px; right: 30px; display: flex; gap: 8px;">
            <div style="width: 12px; height: 12px; background: #F472B6; border-radius: 50%; opacity: 0.4;"></div>
            <div style="width: 12px; height: 12px; background: #A78BFA; border-radius: 50%; opacity: 0.4;"></div>
            <div style="width: 12px; height: 12px; background: #FBBF24; border-radius: 50%; opacity: 0.4;"></div>
        </div>
        
        <div style="display: flex; align-items: center; gap: 24px;">
            @if($avatar)
            <div style="width: 85px; height: 85px; border-radius: 50%; overflow: hidden; border: 4px solid white; flex-shrink: 0; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 26px; font-weight: 700; color: #1F2937; margin: 0 0 6px 0;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; color: #6B7280; margin: 0; line-height: 1.5; max-width: 480px;">{{ Str::limit($cv->objective, 120) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info --}}
        <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 16px; font-size: 12px; color: #4B5563;">
            @if($email)
            <span style="background: white; padding: 5px 12px; border-radius: 20px; font-weight: 500;">{{ $email }}</span>
            @endif
            @if($phone)
            <span style="background: white; padding: 5px 12px; border-radius: 20px;">{{ $phone }}</span>
            @endif
            @if($address)
            <span style="background: white; padding: 5px 12px; border-radius: 20px;">{{ $address }}</span>
            @endif
            @if($linkedin)
            <span style="background: white; padding: 5px 12px; border-radius: 20px;">in {{ $linkedin }}</span>
            @endif
            @if($github)
            <span style="background: white; padding: 5px 12px; border-radius: 20px;">{{ $github }}</span>
            @endif
        </div>
    </div>

    {{-- BODY --}}
    <div style="padding: 28px 40px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 22px;">
                {{-- Section Title --}}
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
                    <div style="width: 8px; height: 8px; background: #F472B6; border-radius: 50%;"></div>
                    <div style="width: 8px; height: 8px; background: #A78BFA; border-radius: 50%;"></div>
                    <div style="width: 8px; height: 8px; background: #FBBF24; border-radius: 50%;"></div>
                    <h2 style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #1F2937; margin: 0 0 0 8px;">{{ $section->title }}</h2>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1F2937;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #9CA3AF; background: #F3F4F6; padding: 3px 10px; border-radius: 12px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 13px; color: #F472B6; font-weight: 600; margin-top: 4px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #6B7280; margin-top: 8px; line-height: 1.6;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 12px; padding: 12px; background: white; border-radius: 12px; display: flex; align-items: center; gap: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                        <div style="width: 10px; height: 10px; background: #A78BFA; border-radius: 50%;"></div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <strong style="font-size: 14px; color: #1F2937;">{{ $c['degree'] ?? '' }}</strong>
                                <span style="font-size: 11px; color: #9CA3AF;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                            </div>
                            <div style="font-size: 12px; color: #A78BFA; font-weight: 600; margin-top: 2px;">{{ $c['school'] ?? '' }}</div>
                        </div>
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 12px; color: #7C3AED; background: #EDE9FE; padding: 6px 14px; border-radius: 20px; font-weight: 500;">
                                {{ $s['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding: 12px; background: white; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                        <div>
                            <strong style="font-size: 13px; color: #1F2937;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 12px; color: #9CA3AF;"> · {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #9CA3AF;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1F2937;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: #A78BFA; text-decoration: none;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #6B7280; margin-top: 8px; line-height: 1.6;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding: 12px; background: white; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 8px; height: 8px; background: #FBBF24; border-radius: 50%;"></div>
                            <span style="font-size: 13px; color: #1F2937;">{{ $c['name'] ?? '' }}</span>
                        </div>
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #9CA3AF;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 12px; background: white; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <strong style="font-size: 13px; color: #1F2937;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: #A78BFA; font-weight: 600; margin-top: 2px;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #9CA3AF; margin-top: 2px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #6B7280; line-height: 1.6; margin-bottom: 6px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
