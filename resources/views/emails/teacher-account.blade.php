@extends('emails.layouts.main')

@section('content')
<!-- Header -->
<div class="email-header">
    <div class="email-logo">👨‍🏫</div>
    <h1>Welcome to Manmohan Memorial Polytechnic!</h1>
    <p>Department of Computer Science & Engineering</p>
</div>

<!-- Body -->
<div class="email-body">
    <h2>Your Faculty Account is Ready!</h2>
    
    <p>Hello <strong>{{ $notifiable->name }}</strong>,</p>
    
    <p>Your faculty account has been successfully created by the administrator. You now have access to the Manmohan Memorial Polytechnic with comprehensive tools to manage your classes, student attendance, examinations, and academic records.</p>

    <!-- Credentials Section -->
    <div class="credentials-box">
        <strong>Your Login Credentials:</strong>
        <div class="credential-item">
            <span class="credential-label">Email:</span>
            <span class="credential-value">{{ $notifiable->email }}</span>
        </div>
        <div class="credential-item">
            <span class="credential-label">Temporary Password:</span>
            <span class="credential-value">{{ $password }}</span>
        </div>
    </div>

    <!-- CTA Button -->
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/dashboard') }}" class="cta-button">Login to Your Account</a>
    </div>

    <!-- Features -->
    <p style="margin-top: 30px; font-weight: 600; color: #1f2937;">Faculty Features Available:</p>
    <ul class="feature-list">
        <li>Manage class attendance and generate reports</li>
        <li>Create and administer examinations</li>
        <li>Enter and manage student marks and grades</li>
        <li>View student performance analytics and insights</li>
        <li>Upload study materials and course resources</li>
        <li>Communicate with students and parents</li>
        <li>Access comprehensive reporting tools</li>
    </ul>

    <!-- Security Warning -->
    <div class="security-note">
        <strong>⚠️ Important Security Notice:</strong> Your temporary password must be changed on your first login. Please create a strong, unique password that only you know to protect your account.
    </div>

    <!-- Additional Info -->
    <div class="info-box">
        <strong>Getting Started:</strong> After logging in, you can configure your class assignments, subject preferences, and other settings from your faculty dashboard.
    </div>

    <p style="margin-top: 30px; color: #6b7280;">Best regards,<br><strong>IT Department Administration</strong><br>Department of Computer Science & Engineering</p>
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

