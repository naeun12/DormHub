<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SendGrid\Mail\Mail as SGMail;
use SendGrid;

class userEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $tenantEmail;
    public $messageBody;

    public function __construct($tenantEmail, $messageBody)
    {
        $this->tenantEmail = $tenantEmail;
        $this->messageBody = $messageBody;
    }

    // Build method: SendGrid API call
    public function build()
    {
        $email = new SGMail();
        $email->setFrom("niiinaeun@gmail.com", "DormHub");
        $email->setSubject("DormHub Reminder");
        $email->addTo($this->tenantEmail);
        $email->addContent("text/plain", $this->messageBody);

        $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));

        try {
            $response = $sendgrid->send($email);
            \Log::info("SendGrid Status: " . $response->statusCode());
        } catch (\Exception $e) {
            \Log::error("SendGrid API Error: " . $e->getMessage());
        }

        return $this;
    }
}
