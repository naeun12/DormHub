<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\userEmail;


class LandingPageController extends Controller
{
    public function landingPage()
    {
        return view('landingpage.landingpage');
    }

   public function sendEmail(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'message' => 'required|string|max:1000',
    ]);

    Mail::to($request->email)->send(new UserEmail($request->name, $request->message));

    return back()->with('success', 'Email sent successfully!');
}
}
