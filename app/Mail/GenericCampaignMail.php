<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $htmlBody;
    public $attachmentPath;

    /**
     * Create a new message instance.
     *
     * @param  string  $subjectLine
     * @param  string  $htmlBody
     * @param  string|null  $attachmentPath
     */
    public function __construct(string $subjectLine, string $htmlBody, string $attachmentPath = null)
    {
        $this->subjectLine = $subjectLine;
        $this->htmlBody = $htmlBody;
        $this->attachmentPath = $attachmentPath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->subject($this->subjectLine)
            ->html($this->htmlBody);

        // ✅ Attach file if provided
        if ($this->attachmentPath && file_exists($this->attachmentPath)) {
            $email->attach($this->attachmentPath);
        }

        return $email;
    }
}
