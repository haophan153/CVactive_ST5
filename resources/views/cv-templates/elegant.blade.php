{{--
    Elegant CV Template - Classic elegant style with subtle sophistication
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#8B7355';
    $font       = $cv->font_family ?? 'Georgia';
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

<div class="cv-document" style="font-family: '{{ $font }}', serif; color: #2D2D2D; background: #FAFAFA;">
    
    {{-- HEADER --}}
    <div style="background: linear-gradient(135deg, #F5F5F5 0%, #FFFFFF 100%); padding: 40px 48px; border-bottom: 3px solid {{ $themeColor }};">
        <div style="display: flex; align-items: center; gap: 28px;">
            @if($avatar)
            <div style="width: 90px; height: 90px; border-radius: 50%; overflow: hidden; border: 4px solid {{ $themeColor }}; flex-shrink: 0;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 26px; font-weight: 400; letter-spacing: 2px; color: #1A1A1A; margin: 0 0 8px 0; text-transform: uppercase;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 12px; color: #666; margin: 0; line-height: 1.8; font-style: italic; max-width: 500px;">"{{ Str::limit($cv->objective, 150) }}"</p>
                @endif
            </div>
        </div>

        {{-- Contact info --}}
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; padding-top: 16px; border-top: 1px solid #E8E8E8; font-size: 11px; color: #555;">
            @if($email)
            <span style="display: flex; align-items: center; gap: 6px;">✉ {{ $email }}</span>
            @endif
            @if($phone)
            <span style="display: flex; align-items: center; gap: 6px;">☎ {{ $phone }}</span>
            @endif
            @if($address)
            <span style="display: flex; align-items: center; gap: 6px;">⌖ {{ $address }}</span>
            @endif
            @if($website)
            <span>{{ $website }}</span>
            @endif
            @if($linkedin)
            <span>LinkedIn: {{ $linkedin }}</span>
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
                <div style="margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid {{ $themeColor }}; display: inline-block;">
                    <h2 style="font-size: 14px; font-weight: 400; text-transform: uppercase; letter-spacing: 3px; color: {{ $themeColor }}; margin: 0;">{{ $section->title }}</h2>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 16px; padding-left: 16px; border-left: 2px solid #E0E0E0;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1A1A1A; font-weight: 500;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #888; font-style: italic;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12px; color: {{ $themeColor }}; margin-top: 3px; font-style: italic;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' | ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #555; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 14px; padding-left: 16px; border-left: 2px solid #E0E0E0;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1A1A1A;">{{ $c['degree'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #888; font-style: italic;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 12px; color: {{ $themeColor }}; margin-top: 3px; font-style: italic;">{{ $c['school'] ?? '' }}</div>
                        @if(!empty($c['gpa']))
                        <span style="font-size: 11px; color: #888;">GPA: {{ $c['gpa'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 12px; color: #333; background: #F0F0F0; padding: 4px 14px; border-radius: 2px;">{{ $s['name'] ?? '' }}</span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding-left: 16px; border-left: 2px solid #E0E0E0;">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['issuer']))
                        <span style="font-size: 12px; color: #666;"> - {{ $c['issuer'] }}</span>
                        @endif
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #888; float: right;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding-left: 16px; border-left: 2px solid #E0E0E0;">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['url']))
                        <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; margin-left: 8px;">{{ $c['url'] }}</a>
                        @endif
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #888; margin-top: 2px; font-style: italic;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #555; margin-top: 6px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding-left: 16px; border-left: 2px solid #E0E0E0;">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['organization']))
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-style: italic;">{{ $c['organization'] }}</div>
                        @endif
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #888; float: right;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 12px; background: #F8F8F8; border-left: 3px solid {{ $themeColor }};">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: {{ $themeColor }};">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #666; margin-top: 4px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' | ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #555; line-height: 1.8; margin-bottom: 8px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
