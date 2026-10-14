{{-- FAQ section of the info pages: $faqs (PageLists records question_* /
answer_*, Arabic falls back to English), $eyebrow / $heading. No question
with text = not shown. Answers are plain text (line breaks kept). --}}
@php
    $faqLocale = app()->getLocale();
    $faqItems = array_values(array_filter($faqs ?? [], fn ($f) => \App\Support\PageLists::text($f, 'question', $faqLocale) !== ''));
@endphp
@if (count($faqItems))
    <section class="w-section{{ !empty($band) ? ' w-band' : '' }}" data-info-faq>
        <div class="w-container">
            @if (trim((string) ($eyebrow ?? '')) !== '' || trim((string) ($heading ?? '')) !== '')
                <div class="w-section-head w-center">
                    @if (trim((string) ($eyebrow ?? '')) !== '')<span class="w-eyebrow">{{ $eyebrow }}</span>@endif
                    @if (trim((string) ($heading ?? '')) !== '')<h2>{{ $heading }}</h2>@endif
                </div>
            @endif
            <div class="w-faq">
                @foreach ($faqItems as $faq)
                    <details>
                        <summary>{{ \App\Support\PageLists::text($faq, 'question', $faqLocale) }}</summary>
                        @if (\App\Support\PageLists::text($faq, 'answer', $faqLocale) !== '')
                            <p>{!! nl2br(e(\App\Support\PageLists::text($faq, 'answer', $faqLocale))) !!}</p>
                        @endif
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif
