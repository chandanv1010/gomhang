{{--
    Floating Zalo / call buttons, bottom-right.

    Numbers come from the systems table: `social_zalo` for the Zalo account and
    the sales hotline for the call button (see system_phone()). Nothing here is
    hardcoded, so the numbers can be changed in the admin.
--}}
@php
    $zaloRaw = trim((string) ($system['social_zalo'] ?? ''));
    // Zalo deep links take the plain digits of the account's phone number.
    $zaloDigits = preg_replace('/\D+/', '', $zaloRaw);
    if ($zaloDigits === '') {
        $zaloDigits = system_phone($system ?? null, true);
    }

    $callDisplay = system_phone($system ?? null);
    $callDigits = system_phone($system ?? null, true);
@endphp

@if($zaloDigits !== '' || $callDigits !== '')
    <div class="floating-contact" aria-label="Liên hệ nhanh">
        @if($zaloDigits !== '')
            <a href="https://zalo.me/{{ $zaloDigits }}" target="_blank" rel="noopener"
               class="floating-contact__btn floating-contact__btn--zalo"
               aria-label="Chat Zalo{{ $zaloRaw !== '' ? ' ' . $zaloRaw : '' }}">
                <span class="floating-contact__ring" aria-hidden="true"></span>
                {{-- Zalo wordmark, inline so it needs no extra request and works offline. --}}
                <svg class="floating-contact__icon" viewBox="0 0 48 48" role="img" aria-hidden="true" focusable="false">
                    <path fill="currentColor" d="M24 4C12.4 4 3 12.1 3 22.1c0 5.7 3 10.7 7.8 14-.3 2.6-1.3 5-2.6 6.8-.4.6.2 1.3.9 1.1 3.6-1 6.5-2.6 8.6-4.1 2 .5 4.1.8 6.3.8 11.6 0 21-8.1 21-18.1S35.6 4 24 4z"/>
                    <path fill="#fff" d="M14.6 16.2h7.7v1.9l-5 6.6h5.2v2H14v-1.9l5-6.6h-4.4v-2zm10.1 0h2.2v10.5h-2.2V16.2zm5.9 3c1 0 1.8.4 2.3 1v-.9h2.1v7.4h-2.1v-.9c-.5.7-1.3 1.1-2.3 1.1-2 0-3.5-1.6-3.5-3.9s1.5-3.8 3.5-3.8zm.4 1.9c-1 0-1.8.8-1.8 1.9s.8 2 1.8 2 1.8-.8 1.8-2-.7-1.9-1.8-1.9zm7.9-1.9c2.1 0 3.8 1.7 3.8 3.9s-1.7 3.9-3.8 3.9-3.8-1.7-3.8-3.9 1.7-3.9 3.8-3.9zm0 1.9c-1 0-1.7.9-1.7 2s.8 2 1.7 2c1 0 1.7-.9 1.7-2s-.7-2-1.7-2z"/>
                </svg>
            </a>
        @endif

        @if($callDigits !== '')
            <a href="tel:{{ $callDigits }}"
               class="floating-contact__btn floating-contact__btn--call"
               aria-label="Gọi {{ $callDisplay }}">
                <span class="floating-contact__ring" aria-hidden="true"></span>
                <i class="fa fa-phone" aria-hidden="true"></i>
                <span class="floating-contact__label">{{ $callDisplay }}</span>
            </a>
        @endif
    </div>

    <style>
    .floating-contact {
        position: fixed;
        right: 16px;
        bottom: 18px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }
    .floating-contact__btn {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        /* 52px keeps it comfortably above the 44px minimum touch target. */
        width: 52px;
        height: 52px;
        border-radius: 50%;
        color: #fff !important;
        text-decoration: none !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .22);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .floating-contact__btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, .28);
    }
    .floating-contact__btn--zalo {
        background: #0068ff; /* Zalo brand blue */
    }
    .floating-contact__btn--call {
        background: #d61c00; /* Site brand red */
    }
    .floating-contact__icon {
        width: 30px;
        height: 30px;
        position: relative;
        z-index: 1;
    }
    .floating-contact__btn--call .fa {
        font-size: 22px;
        position: relative;
        z-index: 1;
    }
    /* The number appears on hover on desktop; the circle alone is enough on a phone. */
    .floating-contact__label {
        position: absolute;
        right: 100%;
        margin-right: 10px;
        padding: 6px 12px;
        border-radius: 20px;
        background: #d61c00;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transform: translateX(6px);
        transition: opacity .2s ease, transform .2s ease;
    }
    .floating-contact__btn--call:hover .floating-contact__label {
        opacity: 1;
        transform: translateX(0);
    }
    /* Expanding ring, the usual "call me" cue on Vietnamese storefronts. */
    .floating-contact__ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: inherit;
        opacity: .55;
        animation: floating-contact-pulse 1.8s ease-out infinite;
    }
    .floating-contact__btn--zalo .floating-contact__ring { background: #0068ff; }
    .floating-contact__btn--call .floating-contact__ring { background: #d61c00; }

    @keyframes floating-contact-pulse {
        0%   { transform: scale(1);   opacity: .55; }
        70%  { transform: scale(1.6); opacity: 0; }
        100% { transform: scale(1.6); opacity: 0; }
    }
    /* Respect a reduced-motion preference instead of pulsing regardless. */
    @media (prefers-reduced-motion: reduce) {
        .floating-contact__ring { animation: none; opacity: 0; }
        .floating-contact__btn { transition: none; }
    }

    @media (max-width: 767px) {
        .floating-contact {
            right: 12px;
            bottom: 12px;
            gap: 10px;
        }
        .floating-contact__btn {
            width: 48px;
            height: 48px;
        }
        .floating-contact__label { display: none; }
    }
    </style>
@endif
