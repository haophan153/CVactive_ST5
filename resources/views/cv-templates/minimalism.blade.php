{{--
    Minimalism CV Template - Clean minimalist design, no photo
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#1A1A1A';
    $font       = $cv->font_family ?? 'Arial';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #1A1A1A; background: #FFFFFF;">
    
    {{-- HEADER - Simple and clean, no avatar --}}
    <div style="padding: 40px 48px 32px; border-bottom: 2px solid #E5E5E5;">
        <h1 style="font-size: 28px; font-weight: 700; letter-spacing: -0.5px; color: #1A1A1A; margin: 0 0 12px 0;">{{ $fullName }}</h1>
        
        @if($cv->objective)
        <p style="font-size: 14px; color: #666; margin: 0 0 20px 0; line-height: 1.6; max-width: 600px;">{{ Str::limit($cv->objective, 180) }}</p>
        @endif

        {{-- Simple contact list --}}
        <div style="display: flex; flex-wrap: wrap; gap: 16px; font-size: 13px; color: #666;">
            @if($email)
            <span>{{ $email }}</span>
            @endif
            @if($phone)
            <span>{{ $phone }}</span>
            @endif
            @if($address)
            <span>{{ $address }}</span>
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
    <div style="padding: 32px 48px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 28px;">
                {{-- Simple section title --}}
                <h2 style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #1A1A1A; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 1px solid #E5E5E5;">
                    {{ $section->title }}
                </h2>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 15px; color: #1A1A1A;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 12px; color: #888;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 14px; color: #666; margin-top: 4px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ', ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 13px; color: #666; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #1A1A1A;">{{ $c['degree'] ?? '' }}</strong>
                            <span style="font-size: 12px; color: #888;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 13px; color: #666; margin-top: 4px;">{{ $c['school'] ?? '' }}</div>
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 13px; color: #1A1A1A;">{{ $s['name'] ?? '' }}</span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                        <span style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</span>
                        @if(!empty($c['date']))
                        <span style="font-size: 12px; color: #888;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px;">
                        <strong style="font-size: 14px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['url']))
                        <span style="font-size: 12px; color: #666; margin-left: 8px;">{{ $c['url'] }}</span>
                        @endif
                        @if(!empty($c['tech']))
                        <div style="font-size: 12px; color: #888; margin-top: 4px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 13px; color: #666; margin-top: 6px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px;">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['organization']))
                        <span style="font-size: 12px; color: #666; margin-left: 6px;">{{ $c['organization'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px;">
                        <strong style="font-size: 13px; color: #1A1A1A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: #666;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 12px; color: #888;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' | ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #666; line-height: 1.7; margin-bottom: 8px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
