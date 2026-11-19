<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($request)
    {
        $this->data = $request;
    }

    public function build()
    {
        return $this->from($this->data->sender_email)
            ->subject($this->data->subject)
            ->view('emails.invitation')
            ->with([
                'data' => $this->data
            ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'reports.invitations',
        );
    }
}
