{{-- Sticky right-hand buttons: back-to-top and a call button wired to the
     number from the admin panel (Settings → Phone).
     On touch devices the phone icon is a plain tel: link (dials straight away);
     on desktop, where there's usually no tel: handler, clicking it opens a
     small popover showing the number (click the number to copy it). --}}
@php($sitePhone = setting('site.phone'))
@php($sitePhoneE164 = setting('site.phone_e164'))
<div class="mil-right-buttons-frame">
    <div class="mil-call-popover" hidden>
        <span class="mil-call-popover-label">Call us</span>
        <a href="tel:{{ $sitePhoneE164 }}" class="mil-call-popover-number" data-no-swup data-phone="{{ $sitePhoneE164 }}">{{ $sitePhone }}</a>
        <span class="mil-call-popover-hint">Click number to copy</span>
    </div>
    <div class="mil-right-buttons">
        <a href="#top" class="mil-side-btn mil-back-to-top mil-hover-bri-120">
            <i class="far fa-arrow-up mil-a-1"></i>
        </a>
        <a href="tel:{{ $sitePhoneE164 }}" class="mil-side-btn mil-call-btn mil-hover-bri-120" data-no-swup aria-label="Call {{ $sitePhone }}"></a>
    </div>
</div>
