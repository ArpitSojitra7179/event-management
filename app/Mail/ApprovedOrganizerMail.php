<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovedOrganizerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;
    public $reason;

    public function __construct($user)
    {
        $this->user = $user;
        $meta = $user->metas()->where('key', 'organizer_request')->latest()->first();

        if ($meta) {
            // $value = json_encode($meta->value);
            // $metaArray = json_decode($value);

            // $this->status = $metaArray->status;

            $data = is_array($meta->value) ? $meta->value : json_decode($meta->value, true);

            $this->status = $data['status'] ?? null;

            if ($this->status == 'rejected') {
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
            subject: 'Approved Organizer Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.approved-organizer',
            with: [
                'user' => $this->user,
                'status' => $this->status,
                'reason' => $this->reason,
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
