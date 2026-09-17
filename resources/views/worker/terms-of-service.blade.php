@extends('worker.layouts.app')

@section('title', 'Terms of Service — Qamr Worker Portal')
@section('meta_description', 'Read the Terms of Service governing use of the Qamr International Worker Portal at worker.qamarhire.com.')

@section('content')

    @php
        $legalAddr = $frontwebsite->bottom_contact_us_addr ?? null;
        $legalPhone = $frontwebsite->bottom_contact_us_phone ?? ($frontwebsite->contact_us_phone ?? null);
        $legalEmail = $frontwebsite->bottom_contact_us_email ?? null;
    @endphp

    <section class="w-page-header">
        <div class="w-container">
            <div class="w-breadcrumb">
                <a href="{{ route('worker.home') }}">{{ __('locale.Home') }}</a>
                <span>/</span>
                <span>{{ __('locale.Terms of Service') }}</span>
            </div>
            <h1>{{ __('locale.Terms of Service') }}</h1>
            <p>{{ __('locale.The terms that govern access to and use of the Qamr International Worker Portal.') }}</p>
        </div>
    </section>

    <section class="w-section">
        <div class="w-container">
            <div class="w-legal w-fade" style="max-width:900px;margin-inline:auto;">

                <span class="w-legal-updated">{{ __('locale.Last updated') }}: {{ __('locale.September 5, 2026') }}</span>

                <nav class="w-legal-toc" aria-label="{{ __('locale.On this page') }}">
                    <span>{{ __('locale.On this page') }}</span>
                    <a href="#acceptance">{{ __('locale.Acceptance of Terms') }}</a>
                    <a href="#eligibility">{{ __('locale.Eligibility & Partner Registration') }}</a>
                    <a href="#account-security">{{ __('locale.Account Security') }}</a>
                    <a href="#use-of-portal">{{ __('locale.Use of the Portal') }}</a>
                    <a href="#candidate-browsing">{{ __('locale.Candidate Browsing & Confidentiality') }}</a>
                    <a href="#hiring-orders">{{ __('locale.Hiring, Orders & Cancellations') }}</a>
                    <a href="#fees-payments">{{ __('locale.Fees, Payments & Refunds') }}</a>
                    <a href="#employer-plus">{{ __('locale.Employer Plus & Additional Services') }}</a>
                    <a href="#user-responsibilities">{{ __('locale.User Responsibilities & Prohibited Conduct') }}</a>
                    <a href="#intellectual-property">{{ __('locale.Intellectual Property') }}</a>
                    <a href="#third-party-services">{{ __('locale.Third-Party Services & Links') }}</a>
                    <a href="#disclaimers">{{ __('locale.Disclaimers') }}</a>
                    <a href="#limitation-of-liability">{{ __('locale.Limitation of Liability') }}</a>
                    <a href="#indemnification">{{ __('locale.Indemnification') }}</a>
                    <a href="#suspension-termination">{{ __('locale.Suspension & Termination') }}</a>
                    <a href="#governing-law">{{ __('locale.Governing Law & Dispute Resolution') }}</a>
                    <a href="#terms-changes">{{ __('locale.Changes to These Terms') }}</a>
                    <a href="#contact-us">{{ __('locale.Contact Us') }}</a>
                </nav>

                <div class="w-info-card" id="acceptance">
                    <h2>1. {{ __('locale.Acceptance of Terms') }}</h2>
                    <p>{{ __('locale.These Terms of Service ("Terms") govern access to and use of the Qamr International Worker Portal at worker.qamarhire.com (the "Portal") by recruitment partners, employers and authorized users ("Partner", "you"). By registering for, accessing or using the Portal, you agree to be bound by these Terms and our Privacy Policy. If you do not agree, please do not use the Portal.') }}</p>
                </div>

                <div class="w-info-card" id="eligibility">
                    <h2>2. {{ __('locale.Eligibility & Partner Registration') }}</h2>
                    <p>{{ __('locale.The Portal is intended for businesses and individuals seeking to hire candidates through Qamr International. To use Partner features, you must register with accurate company and contact information, verify your mobile number, and be approved by our team; access to the Partner Portal is granted only once your registration status is approved. We may decline or revoke registration at our discretion.') }}</p>
                </div>

                <div class="w-info-card" id="account-security">
                    <h2>3. {{ __('locale.Account Security') }}</h2>
                    <p>{{ __('locale.You are responsible for maintaining the confidentiality of your registered mobile number, OTP codes and any linked Google account, and for all activity that occurs under your account. Notify us immediately if you suspect unauthorized access to or use of your account.') }}</p>
                </div>

                <div class="w-info-card" id="use-of-portal">
                    <h2>4. {{ __('locale.Use of the Portal') }}</h2>
                    <p>{{ __('locale.You agree to use the Portal only for legitimate recruitment purposes, to provide accurate information, and not to misuse, disrupt, reverse-engineer or attempt to gain unauthorized access to the Portal or its underlying systems.') }}</p>
                </div>

                <div class="w-info-card" id="candidate-browsing">
                    <h2>5. {{ __('locale.Candidate Browsing & Confidentiality') }}</h2>
                    <p>{{ __('locale.Candidate resumes made available through the Portal are provided to help you evaluate and hire suitable candidates. You agree to keep candidate information confidential, use it only for genuine hiring purposes, and not to copy, redistribute or share it with third parties outside a legitimate hiring process.') }}</p>
                </div>

                <div class="w-info-card" id="hiring-orders">
                    <h2>6. {{ __('locale.Hiring, Orders & Cancellations') }}</h2>
                    <p>{{ __('locale.Submitting a "Hire Now" request creates an order for the selected candidate, subject to the candidate\'s continued availability and eligibility for your selected work location. Orders may be cancelled from the Orders section of the Portal, subject to any processing already undertaken; cancellation does not automatically reverse fees already incurred or services already rendered by our team.') }}</p>
                </div>

                <div class="w-info-card" id="fees-payments">
                    <h2>7. {{ __('locale.Fees, Payments & Refunds') }}</h2>
                    <p>{{ __('locale.Applicable service fees, if any, are as communicated to you by our team or shown on the Portal at the time of placing an order, and are subject to change. Payment terms, invoicing and any refund eligibility will be confirmed directly with you by our recruitment team in accordance with our then-current commercial terms.') }}</p>
                    <div class="w-legal-note">
                        [{{ __('locale.Detailed payment methods, invoicing schedule and refund/cancellation policy to be confirmed and inserted here.') }}]
                    </div>
                </div>

                <div class="w-info-card" id="employer-plus">
                    <h2>8. {{ __('locale.Employer Plus & Additional Services') }}</h2>
                    <p>{{ __('locale.Where you use Employer Plus or other additional features made available through the Portal, such use is subject to these Terms and any additional terms communicated to you for that specific service.') }}</p>
                </div>

                <div class="w-info-card" id="user-responsibilities">
                    <h2>9. {{ __('locale.User Responsibilities & Prohibited Conduct') }}</h2>
                    <p>{{ __('locale.You agree not to:') }}</p>
                    <ul>
                        <li>{{ __('locale.submit false or misleading registration or profile information;') }}</li>
                        <li>{{ __('locale.use the Portal to harass, discriminate against, or unlawfully treat candidates;') }}</li>
                        <li>{{ __('locale.attempt to bypass mobile verification or security controls;') }}</li>
                        <li>{{ __('locale.use automated tools to scrape or extract data from the Portal; or') }}</li>
                        <li>{{ __('locale.use the Portal in violation of applicable laws and regulations, including labor and recruitment regulations.') }}</li>
                    </ul>
                </div>

                <div class="w-info-card" id="intellectual-property">
                    <h2>10. {{ __('locale.Intellectual Property') }}</h2>
                    <p>{{ __('locale.All content, branding, software and materials made available on the Portal, other than candidate-submitted information, are owned by or licensed to Qamr International and may not be copied, modified or used without our prior written consent.') }}</p>
                </div>

                <div class="w-info-card" id="third-party-services">
                    <h2>11. {{ __('locale.Third-Party Services & Links') }}</h2>
                    <p>{{ __('locale.The Portal may rely on or link to third-party services, including WhatsApp/Meta messaging, Google sign-in and other providers. We are not responsible for the content, policies or practices of third-party services, which are governed by their own terms and privacy policies.') }}</p>
                </div>

                <div class="w-info-card" id="disclaimers">
                    <h2>12. {{ __('locale.Disclaimers') }}</h2>
                    <p>{{ __('locale.The Portal and the candidate information made available through it are provided "as is" and "as available." While we take reasonable steps to verify candidate profiles, we do not guarantee the accuracy, completeness or suitability of any candidate for a particular role, and hiring decisions remain your responsibility.') }}</p>
                </div>

                <div class="w-info-card" id="limitation-of-liability">
                    <h2>13. {{ __('locale.Limitation of Liability') }}</h2>
                    <p>{{ __('locale.To the maximum extent permitted by law, Qamr International shall not be liable for any indirect, incidental, special or consequential damages arising from your use of the Portal or reliance on candidate information, including losses arising from hiring decisions.') }}</p>
                </div>

                <div class="w-info-card" id="indemnification">
                    <h2>14. {{ __('locale.Indemnification') }}</h2>
                    <p>{{ __('locale.You agree to indemnify and hold Qamr International harmless from any claims, damages or expenses arising from your misuse of the Portal, breach of these Terms, or violation of applicable law.') }}</p>
                </div>

                <div class="w-info-card" id="suspension-termination">
                    <h2>15. {{ __('locale.Suspension & Termination') }}</h2>
                    <p>{{ __('locale.We may suspend or terminate your access to the Portal at any time, with or without notice, if we reasonably believe you have violated these Terms, provided false information, or engaged in conduct harmful to Qamr International, candidates or other users.') }}</p>
                </div>

                <div class="w-info-card" id="governing-law">
                    <h2>16. {{ __('locale.Governing Law & Dispute Resolution') }}</h2>
                    <p>{{ __('locale.These Terms are governed by the laws of') }} [{{ __('locale.governing jurisdiction to be confirmed') }}]. {{ __('locale.Any disputes arising from these Terms or your use of the Portal shall be subject to the exclusive jurisdiction of the courts of') }} [{{ __('locale.venue to be confirmed') }}], {{ __('locale.unless otherwise required by applicable law.') }}</p>
                </div>

                <div class="w-info-card" id="terms-changes">
                    <h2>17. {{ __('locale.Changes to These Terms') }}</h2>
                    <p>{{ __('locale.We may update these Terms from time to time. Material changes will be reflected by a revised "Last updated" date on this page, and your continued use of the Portal after changes take effect constitutes your acceptance of the revised Terms.') }}</p>
                </div>

                <div class="w-info-card" id="contact-us">
                    <h2>18. {{ __('locale.Contact Us') }}</h2>
                    <p>{{ __('locale.Questions about these Terms can be directed to us using the details below.') }}</p>
                    <ul>
                        @if ($legalEmail)
                            <li><strong>{{ __('locale.Email') }}:</strong> <a href="mailto:{{ $legalEmail }}">{{ $legalEmail }}</a></li>
                        @endif
                        @if ($legalPhone)
                            <li><strong>{{ __('locale.Phone') }}:</strong> <a href="tel:{{ $legalPhone }}">{{ $legalPhone }}</a></li>
                        @endif
                        @if ($legalAddr)
                            <li><strong>{{ __('locale.Registered Address') }}:</strong> {{ $legalAddr }}</li>
                        @endif
                        <li><strong>{{ __('locale.Company Registration No.') }}:</strong> [{{ __('locale.Company Registration / License Number') }}]</li>
                    </ul>
                    <div class="w-legal-note">
                        {{ __('locale.The company, jurisdiction and payment details on this page are placeholders where not yet confirmed and will be updated once verified.') }}
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
