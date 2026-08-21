<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead)
    {
        //
    }

    public function envelope(): Envelope
    {
        $sourceLabel = $this->lead->source === Lead::SOURCE_CALLBACK ? 'callback request' : 'contact form';

        return new Envelope(
            subject: "New {$sourceLabel} — {$this->lead->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-lead',
        );
    }
}
