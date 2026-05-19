<?php

namespace Modules\Emails\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DynamicEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $dynamicSubject;
    public string $dynamicBody;

    /**
     * Create a new message instance.
     *
     * @param string $dynamicSubject
     * @param string $dynamicBody
     */
    public function __construct(string $dynamicSubject, string $dynamicBody)
    {
        $this->dynamicSubject = $dynamicSubject;
        $this->dynamicBody = $dynamicBody;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject($this->dynamicSubject)
                    ->html($this->dynamicBody);
    }
}
