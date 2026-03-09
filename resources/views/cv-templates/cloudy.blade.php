{{--
    Cloudy CV Template - Soft, dreamy design with cloud elements
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#A78BFA';
    $accentColor = $cv->theme_color ?? '#C4B5FD';
    $font       = $cv->font_family ?? 'Quicksand';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #374151; background: linear-gradient(180deg, #FAFAFF 0%, #F5F3FF 100%);">
    
    {{-- HEADER with soft background --}}
    <div style="background: linear-gradient(135deg, {{ $themeColor }}15 0%, {{ $accentColor }}25 100%); padding: 36px 40px; position: relative;">
        {{-- Cloud decoration --}}
        <div style="position: absolute; top: -20px; right: 60px; width: 100px; height: 40px; background: white; border-radius: 40px; opacity: 0.6; box-shadow: 0 4px 15px rgba(0,0,0,0.05);"></div>
        <div style="position: absolute; bottom: -15px; left: 40%; width: 60px; height: 25px; background: white; border-radius: 25px; opacity: 0.4;"></div>
        
        <div style="display: flex; align-items: center; gap: 24px;">
            @if($avatar)
            <div style="width: 90px; height: 90px; border-radius: 50%; overflow: hidden; border: 4px solid {{ $themeColor }}40; flex-shrink: 0; box-shadow: 0 6px 20px {{ $themeColor }}20;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 26px; font-weight: 600; color: #1F2937; margin: 0 0 8px 0;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; color: #6B7280; margin: 0; line-height: 1.6; max-width: 480px;">{{ Str::limit($cv->objective, 130) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info with pill badges --}}
        <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; font-size: 12px; color: #6B7280;">
            @if($email)
            <span style="background: white; padding: 6px 14px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">{{ $email }}</span>
            @endif
            @if($phone)
            <span style="background: white; padding: 6px 14px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">{{ $phone }}</span>
            @endif
            @if($address)
            <span style="background: white; padding: 6px 14px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">{{ $address }}</span>
            @endif
            @if($linkedin)
            <span style="background: white; padding: 6px 14px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">in {{ $linkedin }}</span>
            @endif
            @if($github)
            <span style="background: white; padding: 6px 14px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">⚡ {{ $github }}</span>
            @endif
        </div>
    </div>

    {{-- BODY --}}
    <div style="padding: 28px 40px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 24px;">
                {{-- Section Title with soft accent --}}
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                    <div style="width: 10px; height: 10px; background: {{ $themeColor }}; border-radius: 50%;"></div>
                    <h2 style="font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #1F2937; margin: 0;">{{ $section->title }}</h2>
                    <div style="flex: 1; height: 1px; background: linear-gradient(90deg, {{ $accentColor }} 0%, transparent 100%);"></div>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1F2937;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #9CA3AF; background: #F3F4F6; padding: 3px 10px; border-radius: 12px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 13px; color: {{ $themeColor }}; font-weight: 500; margin-top: 4px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #6B7280; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 12px; padding: 12px; background: white; border-radius: 10px; display: flex; align-items: center; gap: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                        <div style="width: 10px; height: 10px; background: {{ $accentColor }}; border-radius: 50%;"></div>
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <strong style="font-size: 14px; color: #1F2937;">{{ $c['degree'] ?? '' }}</strong>
                                <span style="font-size: 11px; color: #9CA3AF;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                            </div>
                            <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 500; margin-top: 2px;">{{ $c['school'] ?? '' }}</div>
                        </div>
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 12px; color: {{ $themeColor }}; background: white; border: 1px solid {{ $accentColor }}; padding: 6px 14px; border-radius: 20px;">
                                {{ $s['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding: 12px; background: white; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 8px; height: 8px; background: {{ $themeColor }}; border-radius: 50%;"></div>
                            <span style="font-size: 13px; color: #1F2937;">{{ $c['name'] ?? '' }}</span>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 11px; color: #9CA3AF;">· {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #9CA3AF;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1F2937;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; text-decoration: none;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #6B7280; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding: 12px; background: white; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 8px; height: 8px; background: {{ $accentColor }}; border-radius: 50%;"></div>
                            <span style="font-size: 13px; color: #1F2937;">{{ $c['name'] ?? '' }}</span>
                        </div>
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #9CA3AF;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 12px; background: white; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                        <strong style="font-size: 13px; color: #1F2937;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 500; margin-top: 2px;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #6B7280; line-height: 1.7; margin-bottom: 8px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
