<?php

// @ DESCRIPTION      => reports
// @ ENDPOINT         => invitations.blade.php
// @ ACCESS           => all members in space section
// @ CREATED BY       => Harindu Ashen
// @ CREATED DATE     => 2024/06/27 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;

class InvitationController extends Controller
{
    public function invitation()
    {
        return view('pages.reports.invitations');
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'sender_email'   => 'required|email',
            'receiver_email' => 'required|email',
            'subject'        => 'required|string',
            'message'        => 'required|string',
        ]);

        try {
            Mail::to($request->receiver_email)
                ->send(new InvitationMail($request));

            return back()->with('success', 'Invitation email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Email sending failed!');
        }
    }
}
