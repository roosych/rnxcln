<?php

namespace App\Http\Controllers;

use App\Http\Requests\CallbackRequest;
use App\Http\Requests\ContactRequest;
use App\Mail\NewLeadReceived;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    private const CONTACT_SUCCESS = 'Thanks — we have your request and will call you back within one business hour.';

    /**
     * Full booking form on the contact page. Submitted via fetch() with
     * Accept: application/json (see public/js/main.js's initAjaxForms) —
     * validation failures already come back as JSON automatically (Laravel's
     * default FormRequest behavior once the request wants JSON), this just
     * has to answer the success path the same way. Still works with plain
     * HTML form submission (no JS) as a fallback, via the redirect branch.
     */
    public function store(ContactRequest $request): RedirectResponse|JsonResponse
    {
        // Bots fill every field they can find, including this one — it's
        // hidden from real visitors by CSS, not `type="hidden"`, which most
        // bots skip. A hit here means silently pretend it worked instead of
        // creating a Lead or sending a "you got a new lead" email.
        if ($this->isSpam($request)) {
            return $this->respond($request, self::CONTACT_SUCCESS);
        }

        $lead = Lead::create($request->validated() + ['source' => Lead::SOURCE_CONTACT_FORM]);
        $this->notify($lead);

        return $this->respond($request, self::CONTACT_SUCCESS);
    }

    private function respond(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('status', $message)->withFragment('form');
    }

    /** Short "order a call" widget pinned to the right edge of every page. */
    public function callback(CallbackRequest $request): RedirectResponse
    {
        $response = back()->with('status', 'Thanks — we will call you back today.');

        if ($this->isSpam($request)) {
            return $response;
        }

        $lead = Lead::create($request->validated() + ['source' => Lead::SOURCE_CALLBACK]);
        $this->notify($lead);

        return $response;
    }

    private function isSpam(ContactRequest|CallbackRequest $request): bool
    {
        return $request->filled('website');
    }

    /**
     * The Lead is already saved regardless — email is just a heads-up, not
     * the record of truth — so a mail failure (bad SMTP config, provider
     * hiccup) logs and moves on instead of turning into a 500 for the visitor.
     */
    private function notify(Lead $lead): void
    {
        try {
            Mail::to(setting('site.email'))->send(new NewLeadReceived($lead));
        } catch (\Throwable $e) {
            Log::error('Failed to send new-lead notification email.', [
                'lead_id' => $lead->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
