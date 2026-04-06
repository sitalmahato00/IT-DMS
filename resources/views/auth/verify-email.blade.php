@php
    $locale = app()->getLocale();
    $title = $locale === 'ne' ? 'इमेल सत्यापन' : 'Email Verification';
    $subtitle = $locale === 'ne' ? 'आपको खातामा अ्गसर गर्नुहोस्' : 'Verify Your Email to Continue';
    $message = $locale === 'ne' 
        ? 'आपको खातामा ओइएसराथी पहुँच गर्न, कृपया हामीले भेजेको सत्यापन लिङ्कमा क्लिक गर्नुहोस्। यदि आपले इमेल प्राप्त गरेनहुन्, हामी अर्को एक पठाउन खुशी छौं।'
        : 'Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just sent to you. If you didn\'t receive it, we\'ll gladly send another.';
    $resendMsg = $locale === 'ne' 
        ? 'यदि आपले इमेल प्राप्त गरेनहुन्, हामी अन्को पठाउन खुशी छौं।'
        : 'If you didn\'t receive the email, we will gladly send you another.';
    $resendBtn = $locale === 'ne' ? 'सत्यापन इमेल पुनः पठाउनुहोस्' : 'Resend Verification Email';
    $logoutBtn = $locale === 'ne' ? 'लग आउट' : 'Log Out';
    $successMsg = $locale === 'ne'
        ? 'नयाँ सत्यापन लिङ्क आपको इमेल ठेगानामा पठाइयो।'
        : 'A new verification link has been sent to the email address you provided.';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - IT Department Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .verify-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        
        .verify-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
        }
        
        .verify-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .verify-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            margin-top: 0;
        }
        
        .verify-header p {
            font-size: 14px;
            opacity: 0.95;
            margin: 0;
        }
        
        .verify-icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: inline-block;
        }
        
        .verify-content {
            padding: 40px 30px;
        }
        
        .verify-subtitle {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .verify-message {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .verify-alert {
            background-color: #dcfce7;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 14px;
            color: #166534;
        }
        
        .verify-alert strong {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .verify-actions {
            display: flex;
            gap: 12px;
            flex-direction: column;
            align-items: center;
        }
        
        .verify-btn {
            display: inline-block;
            padding: 12px 32px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }
        
        .verify-btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
        }
        
        .verify-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
        }
        
        .verify-btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }
        
        .verify-btn-secondary:hover {
            background: #e5e7eb;
        }
        
        .verify-help {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }
        
        .verify-help p {
            font-size: 14px;
            color: #6b7280;
            margin: 10px 0;
        }
        
        .verify-help a {
            color: #dc2626;
            text-decoration: none;
            font-weight: 500;
        }
        
        .verify-help a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="verify-page">
        <div class="verify-container">
            <div class="verify-header">
                <div class="verify-icon">✉️</div>
                <h1>{{ $title }}</h1>
                <p>{{ $subtitle }}</p>
            </div>
            
            <div class="verify-content">
                @if (session('status') == 'verification-link-sent')
                    <div class="verify-alert">
                        <strong>{{ $locale === 'ne' ? '✓ सफल' : '✓ Success' }}</strong>
                        {{ $successMsg }}
                    </div>
                @endif
                
                <div class="verify-message">
                    {{ $message }}
                </div>
                
                <div class="verify-actions">
                    <form method="POST" action="{{ route('verification.send') }}" style="width: 100%;">
                        @csrf
                        <button type="submit" class="verify-btn verify-btn-primary">
                            {{ $resendBtn }}
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('logout') }}" style="width: 100%;">
                        @csrf
                        <button type="submit" class="verify-btn verify-btn-secondary">
                            {{ $logoutBtn }}
                        </button>
                    </form>
                </div>
                
                <div class="verify-help">
                    <p>{{ $locale === 'ne' ? 'सहायता चाहिएको छ?' : 'Need help?' }}</p>
                    <p>
                        <a href="mailto:{{ config('mail.from.address') ?? 'support@example.com' }}">
                            {{ $locale === 'ne' ? 'समर्थन सम्पर्क गर्नुहोस्' : 'Contact Support' }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
