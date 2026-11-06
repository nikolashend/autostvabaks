<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * Internal notification about a new lead. Written in Russian on purpose:
 * it is read by the company, not by the client.
 */
class NewLeadMail extends Mailable
{
    use Queueable, SerializesModels;

    /** Photos that did not fit into the attachment size budget. */
    public int $skippedPhotos = 0;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новая заявка — '.$this->lead->reg_number,
            replyTo: [new Address($this->lead->email)],
        );
    }

    public function content(): Content
    {
        // attachments() is resolved before the view is rendered, so the
        // skipped counter is final by the time the template reads it.
        return new Content(
            view: 'emails.new-lead',
            text: 'emails.new-lead-text',
            with: [
                'lead' => $this->lead,
                'adminUrl' => url('/admin/leads/'.$this->lead->id),
            ],
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        $disk = Storage::disk('local');
        $budget = (int) config('autostvabaks.leads.mail_attachments_max_mb', 18) * 1024 * 1024;
        $attachments = [];
        $this->skippedPhotos = 0;

        foreach ($this->lead->photos ?? [] as $i => $path) {
            if (! $disk->exists($path)) {
                continue;
            }

            $size = $disk->size($path);

            if ($size > $budget) {
                $this->skippedPhotos++;

                continue;
            }

            $budget -= $size;
            $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';

            $attachments[] = Attachment::fromStorageDisk('local', $path)
                ->as(sprintf('%s-foto-%02d.%s', $this->lead->reg_number, $i + 1, $extension));
        }

        return $attachments;
    }
}
