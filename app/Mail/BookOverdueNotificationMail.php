<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Carbon\Carbon;

class BookOverdueNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $overDueBookData;

    /**
     * Create a new message instance.
     */
    public function __construct($overDueBookData)
    {
        $this->overDueBookData = $overDueBookData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('admin@atom.io', 'Admin'),
            subject: 'Book Overdue Notification Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.overdue_notification',
            with: [
                'userName'      =>  $this->overDueBookData->user->name,
                'bookTitle'     =>  $this->overDueBookData->book->title,
                'tillDate'      =>  Carbon::parse($this->overDueBookData->till_date)->format('d-m-Y'),
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
