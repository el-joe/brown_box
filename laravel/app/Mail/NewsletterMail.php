<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $emailSubject,
        public readonly string $bodyHtml,
    ) {
    }

    public function build(): self
    {
        return $this->subject($this->emailSubject)
            ->view('emails.newsletter', ['bodyHtml' => $this->bodyHtml]);
    }
}
