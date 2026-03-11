{{--
    Graphite CV Template - Professional dark gray design
    Variables: $cv (Cv model), $preview (bool, optional)
--}}
@php
    $personal   = $cv->personal_info ?? [];
    $themeColor = $cv->theme_color ?? '#374151';
    $font       = $cv->font_family ?? 'Lato';
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

<div class="cv-document" style="font-family: '{{ $font }}', sans-serif; color: #1F2937; background: #F9FAFB;">
    
    {{-- HEADER - Dark gray accent --}}
    <div style="background: white; padding: 36px 40px; border-left: 6px solid #374151;">
        <div style="display: flex; align-items: center; gap: 24px;">
            @if($avatar)
            <div style="width: 80px; height: 80px; border-radius: 4px; overflow: hidden; border: 2px solid #374151; flex-shrink: 0;">
                <img src="{{ str_starts_with($avatar, 'http') ? $avatar : asset('storage/'.$avatar) }}"
                    alt="{{ $fullName }}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            @endif
            <div style="flex: 1;">
                <h1 style="font-size: 28px; font-weight: 700; color: #111827; margin: 0 0 6px 0; letter-spacing: -0.5px; text-transform: uppercase;">{{ $fullName }}</h1>
                @if($cv->objective)
                <p style="font-size: 13px; color: #6B7280; margin: 0; line-height: 1.5; max-width: 500px;">{{ Str::limit($cv->objective, 130) }}</p>
                @endif
            </div>
        </div>

        {{-- Contact info --}}
        <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 18px; padding-top: 16px; border-top: 1px dashed #D1D5DB; font-size: 12px; color: #4B5563;">
            @if($email)
            <span style="display: flex; align-items: center; gap: 6px;">
                <span style="width: 6px; height: 6px; background: #374151; border-radius: 50%;"></span> {{ $email }}
            </span>
            @endif
            @if($phone)
            <span style="display: flex; align-items: center; gap: 6px;">
                <span style="width: 6px; height: 6px; background: #374151; border-radius: 50%;"></span> {{ $phone }}
            </span>
            @endif
            @if($address)
            <span style="display: flex; align-items: center; gap: 6px;">
                <span style="width: 6px; height: 6px; background: #374151; border-radius: 50%;"></span> {{ $address }}
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
    <div style="padding: 28px 40px;">
        @foreach($sections as $section)
            @if(!$section->is_visible) @continue @endif
            @if(in_array($section->type, ['personal', 'objective'])) @continue @endif

            <div style="margin-bottom: 22px;">
                {{-- Section Title with dark bar --}}
                <div style="display: flex; align-items: center; margin-bottom: 14px;">
                    <div style="width: 4px; height: 20px; background: #374151; margin-right: 10px;"></div>
                    <h2 style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #111827; margin: 0;">{{ $section->title }}</h2>
                </div>

                {{-- Section Items --}}
                @foreach($section->items->sortBy('sort_order') as $item)
                    @php $c = $item->content; @endphp

                    @if($section->type === 'experience')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-radius: 4px; border: 1px solid #E5E7EB;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #111827;">{{ $c['position'] ?? '' }}</strong>
                            <span style="font-size: 11px; color: #6B7280; background: #F3F4F6; padding: 3px 10px; border-radius: 2px;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                        </div>
                        <div style="font-size: 13px; color: #374151; font-weight: 600; margin-top: 4px;">
                            {{ $c['company'] ?? '' }}{{ ($c['company'] ?? '') && ($c['location'] ?? '') ? ' · ' : '' }}{{ $c['location'] ?? '' }}
                        </div>
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #4B5563; margin-top: 8px; line-height: 1.6;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'education')
                    <div style="margin-bottom: 12px; padding: 12px; background: white; border-radius: 4px; border: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 14px; color: #111827;">{{ $c['degree'] ?? '' }}</strong>
                            <div style="font-size: 12px; color: #374151; font-weight: 600; margin-top: 2px;">{{ $c['school'] ?? '' }}</div>
                        </div>
                        <span style="font-size: 11px; color: #6B7280;">{{ $c['start_date'] ?? '' }}{{ ($c['start_date'] ?? '') && ($c['end_date'] ?? '') ? ' - ' : '' }}{{ $c['end_date'] ?? '' }}</span>
                    </div>

                    @elseif($section->type === 'skills')
                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                        @foreach($section->items->sortBy('sort_order') as $skillItem)
                            @php $s = $skillItem->content; @endphp
                            <span style="font-size: 12px; color: white; background: #374151; padding: 5px 12px; border-radius: 2px; font-weight: 500;">
                                {{ $s['name'] ?? '' }}
                            </span>
                        @endforeach
                    </div>

                    @elseif($section->type === 'certifications')
                    <div style="margin-bottom: 10px; padding: 12px; background: white; border-radius: 4px; border: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['issuer']))
                            <span style="font-size: 12px; color: #6B7280;"> · {{ $c['issuer'] }}</span>
                            @endif
                        </div>
                        @if(!empty($c['date']))
                        <span style="font-size: 11px; color: #6B7280;">{{ $c['date'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'projects')
                    <div style="margin-bottom: 14px; padding: 14px; background: white; border-radius: 4px; border: 1px solid #E5E7EB;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <strong style="font-size: 14px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['url']))
                            <a href="{{ $c['url'] }}" style="font-size: 11px; color: #374151; text-decoration: none;">{{ $c['url'] }}</a>
                            @endif
                        </div>
                        @if(!empty($c['tech']))
                        <div style="font-size: 11px; color: #6B7280; margin-top: 4px;">{{ $c['tech'] }}</div>
                        @endif
                        @if(!empty($c['description']))
                        <p style="font-size: 12px; color: #4B5563; margin-top: 8px; line-height: 1.6;">{{ $c['description'] }}</p>
                        @endif
                    </div>

                    @elseif($section->type === 'activities')
                    <div style="margin-bottom: 10px; padding: 12px; background: white; border-radius: 4px; border: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                            @if(!empty($c['organization']))
                            <div style="font-size: 12px; color: #374151;">{{ $c['organization'] }}</div>
                            @endif
                        </div>
                        @if(!empty($c['period']))
                        <span style="font-size: 11px; color: #6B7280;">{{ $c['period'] }}</span>
                        @endif
                    </div>

                    @elseif($section->type === 'references')
                    <div style="margin-bottom: 12px; padding: 12px; background: white; border-radius: 4px; border: 1px solid #374151;">
                        <strong style="font-size: 13px; color: #111827;">{{ $c['name'] ?? '' }}</strong>
                        @if(!empty($c['title']))
                        <div style="font-size: 12px; color: #374151; font-weight: 600; margin-top: 2px;">{{ $c['title'] }}</div>
                        @endif
                        <div style="font-size: 11px; color: #6B7280; margin-top: 2px;">
                            {{ $c['email'] ?? '' }}{{ ($c['email'] ?? '') && ($c['phone'] ?? '') ? ' · ' : '' }}{{ $c['phone'] ?? '' }}
                        </div>
                    </div>

                    @else
                    <p style="font-size: 13px; color: #4B5563; line-height: 1.6; margin-bottom: 6px;">{{ $c['text'] ?? '' }}</p>
                    @endif

                @endforeach
            </div>
        @endforeach
    </div>
</div>
