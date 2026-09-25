<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $code)
    {
        //
    }

    public function build()
    {
        return $this->subject('Your Password Reset Code')
            ->view('emails.password-reset-code')
            ->with(['code' => $this->code]);
    }
}
