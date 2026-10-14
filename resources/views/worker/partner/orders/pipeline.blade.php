{{-- Order stages (App\Support\OrderPipeline - the CRM booking workflow, persisted data only).
$pipeline = OrderPipeline::for($order); $mini = compact table version. --}}
@if (!empty($mini))
    <div class="wp-pipeline is-mini" style="--wp-stages: {{ $pipeline['total'] }};" aria-label="{{ __('locale.Step :current of :total', ['current' => $pipeline['step'], 'total' => $pipeline['total']]) }} · {{ $pipeline['summary'] }}">
        <div class="wp-pipeline-track">
            @foreach ($pipeline['stages'] as $key => $stage)
                <span class="wp-pipeline-seg is-{{ $stage['state'] }}" title="{{ $stage['label'] }}"></span>
            @endforeach
        </div>
        <span class="wp-pipeline-mini-text">{{ __('locale.Step') }} {{ $pipeline['step'] }}/{{ $pipeline['total'] }} · {{ $pipeline['summary'] }}</span>
    </div>
@else
    <div class="wp-pipeline" style="--wp-stages: {{ $pipeline['total'] }};">
        <div class="wp-pipeline-head">
            <span class="wp-pipeline-title">{{ __('locale.Deployment pipeline') }}</span>
            <span class="wp-pipeline-step">{{ __('locale.Step :current of :total', ['current' => $pipeline['step'], 'total' => $pipeline['total']]) }} · <span class="is-{{ $pipeline['tone'] }}">{{ $pipeline['summary'] }}</span></span>
        </div>
        <ol class="wp-pipeline-stages">
            @foreach ($pipeline['stages'] as $key => $stage)
                <li class="is-{{ $stage['state'] }}">
                    <span class="wp-pipeline-seg"></span>
                    <span class="wp-pipeline-label">
                        @if ($stage['state'] === 'done')<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>@endif
                        <span title="{{ $stage['label'] }}">{{ $stage['short'] ?? $stage['label'] }}</span>
                    </span>
                </li>
            @endforeach
        </ol>
    </div>
@endif
