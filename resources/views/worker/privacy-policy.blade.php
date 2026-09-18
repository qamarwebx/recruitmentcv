@extends('worker.layouts.app')

@section('title', 'Privacy Policy — Qamr Worker Portal')
@section('meta_description', 'Learn how Qamr International collects, uses and protects your information on the Worker Portal at recruitmentcv.com.')

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
                <span>{{ __('locale.Privacy Policy') }}</span>
            </div>
            <h1>{{ __('locale.Privacy Policy') }}</h1>
            <p>{{ __('locale.How Qamr International collects, uses, discloses and protects information on the Worker Portal.') }}</p>
        </div>
    </section>

    <section class="w-section">
        <div class="w-container">
            <div class="w-legal w-fade" style="max-width:900px;margin-inline:auto;">

                <span class="w-legal-updated">{{ __('locale.Last updated') }}: {{ __('locale.September 5, 2026') }}</span>

                <nav class="w-legal-toc" aria-label="{{ __('locale.On this page') }}">
                    <span>{{ __('locale.On this page') }}</span>
                    <a href="#introduction">{{ __('locale.Introduction') }}</a>
                    <a href="#information-we-collect">{{ __('locale.Information We Collect') }}</a>
                    <a href="#how-we-use-information">{{ __('locale.How We Use Your Information') }}</a>
                    <a href="#candidate-confidentiality">{{ __('locale.Candidate Information & Confidentiality') }}</a>
                    <a href="#otp-verification">{{ __('locale.Mobile Verification & OTP') }}</a>
                    <a href="#cookies">{{ __('locale.Cookies & Similar Technologies') }}</a>
                    <a href="#information-sharing">{{ __('locale.How We Share Information') }}</a>
                    <a href="#data-security">{{ __('locale.Data Security') }}</a>
                    <a href="#data-retention">{{ __('locale.Data Retention') }}</a>
                    <a href="#your-rights">{{ __('locale.Your Rights & Choices') }}</a>
                    <a href="#childrens-privacy">{{ __("locale.Children's Privacy") }}</a>
                    <a href="#international-transfers">{{ __('locale.International Data Transfers') }}</a>
                    <a href="#policy-changes">{{ __('locale.Changes to This Privacy Policy') }}</a>
                    <a href="#contact-us">{{ __('locale.Contact Us') }}</a>
                </nav>

                <div class="w-info-card" id="introduction">
                    <h2>1. {{ __('locale.Introduction') }}</h2>
                    <p>{{ __('locale.This Privacy Policy explains how Qamr International ("Qamr", "we", "us", "our") collects, uses, discloses and protects information in connection with the Worker Portal available at worker.qamarhire.com (the "Portal"), used by recruitment partners and employers ("Partners") to browse candidate profiles and manage hiring.') }}</p>
                    <p>{{ __('locale.By accessing or using the Portal, you agree to the collection and use of information as described in this Privacy Policy. If you do not agree with this policy, please do not use the Portal.') }}</p>
                </div>

                <div class="w-info-card" id="information-we-collect">
                    <h2>2. {{ __('locale.Information We Collect') }}</h2>
                    <ul>
                        <li><strong>{{ __('locale.Account & Registration Information') }}:</strong> {{ __('locale.company or establishment name, the authorized contact person\'s full name, email address, mobile number and country, submitted when you register as a Partner.') }}</li>
                        <li><strong>{{ __('locale.Verification Data') }}:</strong> {{ __('locale.one-time passcodes (OTP) and verification timestamps used to confirm your mobile number via SMS or WhatsApp, and basic Google account details if you sign in with Google.') }}</li>
                        <li><strong>{{ __('locale.Usage & Activity Data') }}:</strong> {{ __('locale.candidates you view, shortlist, hire or cancel, orders you place, Employer Plus records you manage, and pages you visit within the Portal.') }}</li>
                        <li><strong>{{ __('locale.Communications') }}:</strong> {{ __('locale.messages exchanged with our support team by phone, WhatsApp or email, and notifications we send about your account, orders or registration status.') }}</li>
                        <li><strong>{{ __('locale.Technical Data') }}:</strong> {{ __('locale.IP address, browser type, device information and session or cookie identifiers collected automatically when you use the Portal.') }}</li>
                    </ul>
                </div>

                <div class="w-info-card" id="how-we-use-information">
                    <h2>3. {{ __('locale.How We Use Your Information') }}</h2>
                    <ul>
                        <li>{{ __('locale.To create, verify and manage your Partner account, including reviewing and approving or rejecting registration requests.') }}</li>
                        <li>{{ __('locale.To authenticate you at login using one-time passcodes (OTP) sent via SMS or WhatsApp.') }}</li>
                        <li>{{ __('locale.To enable you to browse candidate resumes and place, track or cancel hiring orders.') }}</li>
                        <li>{{ __('locale.To communicate with you about your account, orders, registration status and service updates.') }}</li>
                        <li>{{ __('locale.To maintain the security, integrity and proper functioning of the Portal, including fraud prevention and abuse detection.') }}</li>
                        <li>{{ __('locale.To comply with applicable legal and regulatory obligations.') }}</li>
                    </ul>
                </div>

                <div class="w-info-card" id="candidate-confidentiality">
                    <h2>4. {{ __('locale.Candidate Information & Confidentiality') }}</h2>
                    <p>{{ __('locale.Candidate resumes and profile details displayed on the Portal are provided to Partners solely to evaluate and hire candidates. Partners must not copy, share, publish or use candidate information for any purpose other than legitimate recruitment through Qamr International, and must handle such information confidentially and in accordance with applicable data protection laws.') }}</p>
                </div>

                <div class="w-info-card" id="otp-verification">
                    <h2>5. {{ __('locale.Mobile Verification & OTP') }}</h2>
                    <p>{{ __('locale.Certain actions on the Portal, including logging in, registering and confirming a hire ("Hire Now"), require verifying a mobile number through a one-time passcode delivered by SMS or WhatsApp. OTP codes are time-limited, used solely for verification, and are not retained beyond what is necessary to complete that verification.') }}</p>
                </div>

                <div class="w-info-card" id="cookies">
                    <h2>6. {{ __('locale.Cookies & Similar Technologies') }}</h2>
                    <p>{{ __('locale.We use cookies and similar technologies, such as session identifiers, to keep you signed in, remember your language preference and understand how the Portal is used. You can control cookies through your browser settings; disabling cookies may affect certain Portal features, such as staying logged in.') }}</p>
                </div>

                <div class="w-info-card" id="information-sharing">
                    <h2>7. {{ __('locale.How We Share Information') }}</h2>
                    <p>{{ __('locale.We do not sell your personal information. We may share information with:') }}</p>
                    <ul>
                        <li>{{ __('locale.service providers who help operate the Portal, such as SMS/WhatsApp messaging providers (including the Meta/WhatsApp Business API) and hosting or cloud infrastructure providers;') }}</li>
                        <li>{{ __('locale.Google, if you choose to sign in with a Google account;') }}</li>
                        <li>{{ __('locale.our internal recruitment and support staff, to process registrations, orders and inquiries; and') }}</li>
                        <li>{{ __('locale.authorities or other third parties where required by law, or to protect our rights, property or safety, or that of our users.') }}</li>
                    </ul>
                </div>

                <div class="w-info-card" id="data-security">
                    <h2>8. {{ __('locale.Data Security') }}</h2>
                    <p>{{ __('locale.We implement reasonable technical and organizational measures, including encrypted transmission, access controls and OTP-based authentication, designed to protect your information from unauthorized access, alteration, disclosure or destruction. However, no method of transmission or storage is completely secure, and we cannot guarantee absolute security.') }}</p>
                </div>

                <div class="w-info-card" id="data-retention">
                    <h2>9. {{ __('locale.Data Retention') }}</h2>
                    <p>{{ __('locale.We retain account, order and communication records for as long as your Partner account remains active, and for a reasonable period afterward as needed to comply with legal obligations, resolve disputes and enforce our agreements.') }}</p>
                </div>

                <div class="w-info-card" id="your-rights">
                    <h2>10. {{ __('locale.Your Rights & Choices') }}</h2>
                    <p>{{ __('locale.Depending on applicable law, you may have the right to access, correct, update or request deletion of your personal information, and to object to or restrict certain processing. You may update your account details from the Profile page of the Portal, or contact us using the details in the "Contact Us" section below to exercise these rights.') }}</p>
                </div>

                <div class="w-info-card" id="childrens-privacy">
                    <h2>11. {{ __("locale.Children's Privacy") }}</h2>
                    <p>{{ __('locale.The Portal is intended for use by business partners and employers and is not directed at individuals under the age of 18. We do not knowingly collect personal information from minors.') }}</p>
                </div>

                <div class="w-info-card" id="international-transfers">
                    <h2>12. {{ __('locale.International Data Transfers') }}</h2>
                    <p>{{ __('locale.Where information is transferred to or accessed from a country other than your own, including where our service providers are located, we take reasonable steps to ensure it continues to be protected in accordance with this Privacy Policy and applicable law.') }}</p>
                </div>

                <div class="w-info-card" id="policy-changes">
                    <h2>13. {{ __('locale.Changes to This Privacy Policy') }}</h2>
                    <p>{{ __('locale.We may update this Privacy Policy from time to time to reflect changes in our practices or for legal, operational or regulatory reasons. The updated version will be posted on this page with a revised "Last updated" date, and continued use of the Portal after changes take effect constitutes acceptance of the revised policy.') }}</p>
                </div>

                <div class="w-info-card" id="contact-us">
                    <h2>14. {{ __('locale.Contact Us') }}</h2>
                    <p>{{ __('locale.If you have questions about this Privacy Policy or how your information is handled, please contact us using the details below.') }}</p>
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
                        {{ __('locale.The company and registration details on this page are placeholders where not yet confirmed and will be updated once verified.') }}
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
