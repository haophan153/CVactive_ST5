{{--
    Deluxe CV Template - Elegant premium design
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#B8860B';
    $font       = $cv->font_family ?? 'Playfair Display';
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

<div class="cv-document" style="font-family: '{{ $font }}', serif; color: #2C2C2C; background: #FFFEF8;">
    
    {{-- HEADER with border design --}}
    <div style="background: linear-gradient(180deg, #FFFEF8 0%, #FEFEFE 100%); padding: 44px 48px; border-bottom: 2px solid {{ $themeColor }}; position: relative;">
        {{-- Corner decorations --}}
        <div style="position: absolute; top: 0; left: 0; width: 30px; height: 30px; border-top: 3px solid {{ $themeColor }}; border-left: 3px solid {{ $themeColor }}; opacity: 0.5;"></div>
        <div style="position: absolute; top: 0; right: 0; width: 30px; height: 30px; border-top: 3px solid {{ $themeColor }}; border-right: 3px solid {{ $themeColor }}; opacity: 0.5;"></div>
        
        <div style="display: flex; align-items: center; gap: 32px;">
            @if($avatar)
            <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 3px solid {{ $themeColor }}; flex-shrink: 0; box-shadow: 0 4px 20px rgba(184, 134, 11, 0.2);">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1; text-align: center;">
                <h1 style="font-size: 32px; font-weight: 600; color: #1A1A1A; margin: 0 0 10px 0; letter-spacing: 2px; text-transform: uppercase;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; color: #666; margin: 0; line-height: 1.7; max-width: 550px; font-style: italic;">{{ Str::limit($cv->objective, 150) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info with line separators --}}
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 24px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #E8E4D9; font-size: 12px; color: #555;">
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
    <div style="padding: 36px 48px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 28px;">
                {{-- Section Title with elegant underline --}}
                <div style="text-align: center; margin-bottom: 18px;">
                    <h2 style="font-size: 15px; font-weight: 600; text-transform: uppercase; letter-spacing: 4px; color: {{ $themeColor }}; margin: 0 0 8px 0;">{{ $section->title }}</h2>
                    <div style="width: 60px; height: 2px; background: {{ $themeColor }}; margin: 0 auto;"></div>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 16px; padding: 16px; background: #FFFEF8; border-left: 3px solid {{ $themeColor }};">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 15px; color: #1A1A1A;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #888; font-style: italic;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 13px; color: {{ $themeColor }}; margin-top: 4px; font-weight: 500;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' | ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #555; margin-top: 10px; line-height: 1.8;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 14px; padding: 14px; background: #FFFEF8; text-align: center;">
                        <strong style="font-size: 15px; color: #1A1A1A;">{{ $c['degree'] ?? '' }}</strong>
                        <div style="font-size: 13px; color: {{ $themeColor }}; margin-top: 4px;">{{ $c['school'] ?? '' }}</div>
                        <span style="font-size: 11px; color: #888;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        @if(!empty($c['gpa']))
                        <span style="font-size: 11px; color: #888; margin-left: 10px;">GPA: {{ $c['gpa'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="text-align: center;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="display: inline-block; font-size: 12px; color: #333; background: linear-gradient(135deg, #FEFEFE, #F8F4E8); border: 1px solid #E8E4D9; padding: 6px 16px; margin: 4px; border-radius: 2px;">
                                {{ $s['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; text-align: center; padding: 10px; background: #FFFEF8;">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['issuer']))
                        <span style="font-size: 12px; color: #666;"> - {{ $c['issuer'] }}</span>
                        @endif
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #888; margin-left: 10px;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding: 14px; background: #FFFEF8; border-left: 3px solid {{ $themeColor }};">
                        <strong style="font-size: 14px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['url']))
                        <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; margin-left: 8px;">{{ $c['url'] }}</a>
                        @endif
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #888; margin-top: 4px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #555; margin-top: 8px; line-height: 1.8;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; text-align: center; padding: 10px; background: #FFFEF8;">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['organization']))
                        <div style="font-size: 12px; color: {{ $themeColor }};">{{ $c['organization'] }}</div>
                        @endif
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #888;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 14px; background: linear-gradient(135deg, #FFFEF8, #F8F4E8); text-align: center; border: 1px solid #E8E4D9;">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: {{ $themeColor }};">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #666; margin-top: 4px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' | ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #555; line-height: 1.8; margin-bottom: 8px; text-align: center;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
