<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovedPublishedRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $reason;

    public function __construct($event)
    {
        $this->event = $event;

        if ($event->status == 'rejected') {
            $meta = $event->metas()->where('key', 'event_request')->latest()->first();

            if ($meta) {
                // $value = json_encode($meta->value);
                // $metaArray = json_decode($value);

                $data = is_array($meta->value) ? $meta->value : json_decode($meta->value, true);

                $this->reason = $data['reason'] ?? 'no reason provided.';
            }
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Approved Published Request',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
    return new Content(
            markdown: 'mail.approved-eventpublish-request',
            with: [
                'event' => $this->event,
                'reason'=> $this->reason,
            ],
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
