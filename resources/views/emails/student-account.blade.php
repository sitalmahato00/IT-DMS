@extends('emails.layouts.main')

@section('content')
<!-- Header -->
<div class="email-header">
    <div class="email-logo">👤</div>
    <h1>Welcome to IT-DMS!</h1>
    <p>Department of Computer Science & Engineering</p>
</div>

<!-- Body -->
<div class="email-body">
    <h2>Your Account is Ready!</h2>
    
    <p>Hello <strong>{{ $notifiable->name }}</strong>,</p>
    
    <p>Your student account has been successfully created by the administrator. You now have access to the IT Department Management System where you can track your academic progress, attendance, exam results, and more!</p>

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
    <p style="margin-top: 30px; font-weight: 600; color: #1f2937;">You can now:</p>
    <ul class="feature-list">
        <li>View your attendance records and statistics</li>
        <li>Check your exam results and grades</li>
        <li>Access study materials and resources</li>
        <li>Read important notices and announcements</li>
        <li>Update your profile information</li>
        <li>Track your academic performance</li>
    </ul>

    <!-- Security Warning -->
    <div class="security-note">
        <strong>⚠️ Important Security Notice:</strong> Your temporary password should be changed immediately after your first login. Use a strong, unique password that only you know.
    </div>

    <!-- Additional Info -->
    <div class="info-box">
        <strong>Need help?</strong> If you have any issues logging in or accessing your account, please contact the IT Department or your class teacher.
    </div>

    <p style="margin-top: 30px; color: #6b7280;">Best regards,<br><strong>IT Department</strong><br>Department of Computer Science & Engineering</p>
</div>

<!-- Footer -->
<div class="email-footer">
    <p><strong>IT Department Management System (IT-DMS)</strong></p>
    <p>This is an automated message. Please do not reply to this email.</p>
    <p style="margin-top: 15px; color: #9ca3af;">
        © 2026 IT Department. All rights reserved.
    </p>
</div>
@endsection
