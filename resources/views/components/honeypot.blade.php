@props(['suffix' => ''])

{{--
    Spam trap: hidden from real visitors with off-screen positioning (not
    type="hidden" or display:none, which most bots know to skip), so a bot
    that blindly fills every field it finds gives itself away. See
    LeadController::isSpam() — a submission with this filled in is silently
    accepted and dropped, no Lead created, no email sent.

    $suffix keeps the id unique when this shows up twice on one page — the
    callback widget in the header is on every page, including /contact,
    which has its own copy too.
--}}
<div style="position:absolute; left:-9999px; top:-9999px;" aria-hidden="true">
    <label for="website{{ $suffix }}">Leave this field blank</label>
    <input type="text" id="website{{ $suffix }}" name="website" tabindex="-1" autocomplete="off">
</div>
