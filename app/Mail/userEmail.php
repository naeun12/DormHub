<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class userEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantName;
    public $messageBody;

    public function __construct($tenantName, $messageBody)
    {
        $this->tenantName = $tenantName;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('DormHub Reminder')
                    ->view('admin.auth.email.tenantemail');
    }
}

