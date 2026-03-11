{{--
    Circum CV Template - Modern circular design elements
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#0EA5E9';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #1E293B; background: #F8FAFC;">
    
    {{-- HEADER --}}
    <div style="background: white; padding: 40px; border-bottom: 4px solid {{ $themeColor }}; position: relative;">
        {{-- Decorative circles --}}
        <div style="position: absolute; top: 20px; right: 40px; width: 80px; height: 80px; border: 3px solid {{ $themeColor }}; border-radius: 50%; opacity: 0.3;"></div>
        <div style="position: absolute; top: 40px; right: 60px; width: 40px; height: 40px; background: {{ $themeColor }}; border-radius: 50%; opacity: 0.2;"></div>
        
        <div style="display: flex; align-items: center; gap: 28px;">
            @if($avatar)
            <div style="width: 90px; height: 90px; border-radius: 50%; overflow: hidden; border: 4px solid {{ $themeColor }}; flex-shrink: 0; box-shadow: 0 4px 14px rgba(0,0,0,0.1);">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 30px; font-weight: 800; color: #0F172A; margin: 0 0 8px 0; letter-spacing: -1px;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; color: #64748B; margin: 0; line-height: 1.6; max-width: 520px;">{{ Str::limit($cv->objective, 140) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info --}}
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 24px; padding-top: 20px; border-top: 1px dashed #E2E8F0; font-size: 12px; color: #475569;">
            @if($email)
            <span style="display: flex; align-items: center; gap: 6px;">
                <span style="width: 8px; height: 8px; background: {{ $themeColor }}; border-radius: 50%;"></span> {{ $email }}
            </span>
            @endif
            @if($phone)
            <span style="display: flex; align-items: center; gap: 6px;">
                <span style="width: 8px; height: 8px; background: {{ $themeColor }}; border-radius: 50%;"></span> {{ $phone }}
            </span>
            @endif
            @if($address)
            <span style="display: flex; align-items: center; gap: 6px;">
                <span style="width: 8px; height: 8px; background: {{ $themeColor }}; border-radius: 50%;"></span> {{ $address }}
            </span>
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
    <div style="padding: 32px 40px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 24px;">
                {{-- Section Title --}}
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div style="width: 16px; height: 16px; background: {{ $themeColor }}; border-radius: 50%;"></div>
                    <h2 style="font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #0F172A; margin: 0;">{{ $section->title }}</h2>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 16px; padding: 16px; background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <strong style="font-size: 15px; color: #0F172A;">{{ $c['position'] ?? '' }}</strong>
                                <div style="font-size: 13px; color: {{ $themeColor }}; font-weight: 600; margin-top: 4px;">
                                    {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                                </div>
                            </div>
                            <span style="font-size: 11px; color: #94A3B8; background: #F1F5F9; padding: 4px 10px; border-radius: 20px; white-space: nowrap;">
                                {{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}
                            </span>
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #64748B; margin-top: 10px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-radius: 10px; display: flex; align-items: center; gap: 14px; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                        <div style="width: 12px; height: 12px; background: {{ $themeColor }}; border-radius: 50%; flex-shrink: 0;"></div>
                        <div style="flex: 1; display: flex; justify-content: space-between; align-items: baseline;">
                            <div>
                                <strong style="font-size: 14px; color: #0F172A;">{{ $c['degree'] ?? '' }}</strong>
                                <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 2px;">{{ $c['school'] ?? '' }}</div>
                            </div>
                            <span style="font-size: 11px; color: #94A3B8;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        @if(!empty($c['gpa']))
                        <span style="font-size: 11px; color: #64748B;">GPA: {{ $c['gpa'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 12px; color: {{ $themeColor }}; background: white; border: 1.5px solid {{ $themeColor }}; padding: 6px 14px; border-radius: 20px; font-weight: 500;">
                                {{ $s['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding: 12px; background: white; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 10px; height: 10px; background: {{ $themeColor }}; border-radius: 50%;"></div>
                            <div>
                                <strong style="font-size: 13px; color: #0F172A;">{{ $c['name'] ?? '' }}</strong>
                                @if(!empty($c['issuer']))
                                <span style="font-size: 11px; color: #64748B;"> · {{ $c['issuer'] }}</span>
                                @endif
                            </div>
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #94A3B8;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #0F172A;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: {{ $themeColor }}; text-decoration: none;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #94A3B8; margin-top: 4px; font-style: italic;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #64748B; margin-top: 8px; line-height: 1.7;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding: 12px; background: white; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px rgba(0,0,0,0.04);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 10px; height: 10px; background: {{ $themeColor }}; border-radius: 50%;"></div>
                            <div>
                                <strong style="font-size: 13px; color: #0F172A;">{{ $c['name'] ?? '' }}</strong>
                                @if(!empty($c['organization']))
                                <div style="font-size: 11px; color: #64748B;">{{ $c['organization'] }}</div>
                                @endif
                            </div>
                        </div>
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #94A3B8;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 14px; background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <strong style="font-size: 13px; color: #0F172A;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: {{ $themeColor }}; font-weight: 600; margin-top: 2px;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #64748B; margin-top: 4px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #64748B; line-height: 1.7; margin-bottom: 8px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
