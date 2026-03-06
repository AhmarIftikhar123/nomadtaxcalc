<?php

namespace App\Services\Contact;

use App\Mail\ContactSubmissionMail;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    public function handleSubmission(array $data, Request $request): ContactSubmission
    {
        $submission = ContactSubmission::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'subject'    => $data['subject'],
            'message'    => $data['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status'     => 'new',
        ]);

        $notificationEmail = config('mail.contact_notification_email', env('CONTACT_NOTIFICATION_EMAIL', 'coadersworldandais@gmail.com'));
        
        Mail::to($notificationEmail)->send(new ContactSubmissionMail($submission));

        return $submission;
    }
}
