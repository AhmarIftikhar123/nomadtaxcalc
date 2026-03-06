<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <div style="max-w-2xl mx-auto p-4">
        <div style="text-align: center; margin-bottom: 2rem;">
            <img src="{{ $message->embed(public_path('assets/images/logos/logo-desktop.png')) }}" alt="NomadTax" style="height: 40px;">
        </div>
        
        <h2 style="color: #22262a;">New Contact Submission</h2>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee; width: 120px;"><strong>Name:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $submission->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Email:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Subject:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ ucfirst(str_replace('_', ' ', $submission->subject)) }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>IP Address:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $submission->ip_address }}</td>
            </tr>
        </table>

        <h3 style="color: #22262a; margin-bottom: 1rem;">Message:</h3>
        <div style="background-color: #faf8f7; padding: 1.5rem; border-radius: 8px; border: 1px solid #e0e0e1;">
            {!! nl2br(e($submission->message)) !!}
        </div>
    </div>
</body>
</html>
