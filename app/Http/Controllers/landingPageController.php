<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\userEmail;

use Illuminate\Support\Facades\Mail;

class landingPageController extends Controller
{
    public function landingPage()
    {
        return view('landingpage.landingpage');

    }
    public function sendEmail(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        $tenantEmail = $request->email;
        $messageBody = "Hi " . $request->name . ",\n\n" . $request->message;
        $email = new Mail();
        $email->setFrom("niiinaeun@gmail.com", "DormHub");
        $email->setSubject("DormHub Reminder");
        $email->addTo($tenantEmail);
        $email->addContent("text/plain", $messageBody);

        try {
            $sendgrid = new SendGrid(env('SENDGRID_API_KEY'));
            $response = $sendgrid->send($email);
            \Log::info("SendGrid Status: " . $response->statusCode());
        } catch (\Exception $e) {
            \Log::error("SendGrid API Error: " . $e->getMessage());
        }

        return back()->with('success', 'Your message has been sent successfully!');


    }
}
