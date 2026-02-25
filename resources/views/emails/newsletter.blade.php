<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Figtree', sans-serif;
            background-color: #faf8f7; /* LIGHT */
            color: #22262a; /* PRIMARY */
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-top: 40px;
            margin-bottom: 40px;
            overflow: hidden;
        }
        .header {
            text-align: center;
            background-color: #18191a; /* DARK */
            padding: 30px 20px;
        }
        .header img {
            max-height: 40px;
            /* Invert to make the dark logo readable on the dark header */
            filter: brightness(0) invert(1);
        }
        .content {
            padding: 30px;
            color: #22262a;
        }
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #faf8f7;
            border-top: 1px solid #e0e0e1;
            font-size: 13px;
            color: #737578; /* GRAY */
        }
        .footer a {
            color: #22262a;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(file_exists(resource_path('js/assets/images/logos/logo-desktop.png')))
                <img src="{{ $message->embed(resource_path('js/assets/images/logos/logo-desktop.png')) }}" alt="Logo">
            @else
                <h1 style="color: #ffffff; margin: 0;">{{ env('APP_NAME') }}</h1>
            @endif
        </div>
        
        <div class="content">
            {!! nl2br(e($content)) !!}
        </div>
        
        <div class="footer">
            <p>You're receiving this email because you subscribed to our newsletter.</p>
            <p><a href="{{ route('newsletter.unsubscribe', ['token' => $unsubscribeToken]) }}">Unsubscribe</a></p>
        </div>
    </div>
</body>
</html>
