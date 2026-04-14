<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;

final class RegisterApproval extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array{'fromEmail': string, 'fromName': string, 'subject': string, 'url': string} $data
     */
    public array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->data['fromEmail'], $this->data['fromName']),
            subject: $this->data['subject']
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.register-approval',
            with: [
                'url'    => $this->data['url'],
                'logo' => $this->data['logo'] ?? NULL
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
