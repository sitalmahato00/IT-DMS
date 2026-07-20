@extends('emails.layouts.main')

@section('content')
<!-- Header -->
<div class="email-header">
    <div class="email-logo">✉️</div>
    <h1>Verify Your Email</h1>
    <p>Complete Your Account Setup</p>
</div>

<!-- Body -->
<div class="email-body">
    <h2>Email Verification Required</h2>
    
    <p>Hello <strong>{{ $notifiable->name }}</strong>,</p>
    
    <p>Thank you for registering with the Manmohan Memorial Polytechnic. To activate your account and gain full access to all features, please verify your email address by clicking the button below.</p>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $verificationUrl }}" class="cta-button">Verify Email Address</a>
    </div>

    <!-- Alternative Link -->
    <p style="font-size: 13px; color: #6b7280; text-align: center; margin-top: 20px;">
        If the button above doesn't work, copy and paste this link into your browser:<br>
        <span style="word-break: break-all; font-family: monospace; color: #374151;">{{ $verificationUrl }}</span>
    </p>

    <!-- Security Warning -->
    <div class="security-note">
        <strong>Security Note:</strong> This link will expire in 24 hours. If you did not create this account, please ignore this email. Do not share this link with anyone else.
    </div>

    <!-- Benefits -->
    <p style="margin-top: 30px; font-weight: 600; color: #1f2937;">Once verified, you'll have access to:</p>
    <ul class="feature-list">
        <li>View your attendance records</li>
        <li>Check exam results and grades</li>
        <li>Access study materials and resources</li>
        <li>Read important announcements</li>
        <li>Manage your profile information</li>
        <li>Track academic performance</li>
    </ul>

    <!-- Additional Info -->
    <div class="info-box">
        <strong>Having trouble?</strong> If you're having difficulty verifying your email, please contact the IT Department or your department administrator for assistance.
    </div>

    <p style="margin-top: 30px; color: #6b7280;">Best regards,<br><strong>IT Department</strong><br>Manmohan Memorial Polytechnic</p>
</div>

<!-- Footer -->
<div class="email-footer">
    <p><strong>Manmohan Memorial Polytechnic</strong></p>
    <p>This is an automated message. Please do not reply to this email.</p>
    <p style="margin-top: 15px; color: #9ca3af;">
        © 2026 IT Department. All rights reserved.
    </p>
</div>
@endsection

