<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;

final class DefaultEmail extends Mailable
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
            view: 'emails.default-email',
            with: [
                'url'    => $this->data['url'],
                'logo' => $this->data['logo'] ?? NULL,
                'title' => $this->data['title'] ?? 'Informação',
                'heading' => $this->data['heading'],
                'paragraphs' => $this->data['paragraphs'],
                'btnText' => $this->data['btnText'],
                'remain' => $this->data['remain'],
                'regards' => $this->data['regards']
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
