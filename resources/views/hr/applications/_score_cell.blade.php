{{-- AI score badge for a JobApplication row --}}
@php
    $info = $application->ai_score_label;
    $summary = $application->ai_summary ?? '';
@endphp

@if($application->ai_score !== null)
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $info['color'] }}"
          title="{{ $summary !== '' ? $summary . ' (chấm lúc ' . $application->ai_scored_at?->format('d/m/Y H:i') . ')' : 'AI đã chấm' }}">
        <span aria-hidden="true">✨</span>
        {{ $info['label'] }}
    </span>
@else
    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $info['color'] }}"
          title="Chưa chấm điểm AI">
        —
    </span>
@endif