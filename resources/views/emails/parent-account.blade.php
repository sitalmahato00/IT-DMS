@extends('emails.layouts.main')

@section('content')
<!-- Header -->
<div class="email-header">
    <div class="email-logo">👨‍👩‍👧</div>
    <h1>Welcome to Manmohan Memorial Polytechnic!</h1>
    <p>Department of Computer Science & Engineering</p>
</div>

<!-- Body -->
<div class="email-body">
    <h2>Your Parent Account is Ready!</h2>
    
    <p>Hello <strong>{{ $notifiable->name }}</strong>,</p>
    
    <p>Your parent/guardian account has been successfully created by the administrator. You now have access to the Manmohan Memorial Polytechnic where you can monitor your child's academic progress, attendance, examination results, and important notifications from the department.</p>

    @php
        $linkedStudentName = $context['student_name'] ?? null;
        $linkedStudentRollNo = $context['student_roll_no'] ?? null;
        $relationship = $context['relationship'] ?? null;
    @endphp

    @if($linkedStudentName || $linkedStudentRollNo || $relationship)
    <div class="info-box" style="margin-top: 18px;">
        <strong>Linked Student Information</strong>
        <div style="margin-top: 10px; line-height: 1.7;">
            @if($linkedStudentName)
                <div><strong>Student:</strong> {{ $linkedStudentName }}</div>
            @endif
            @if($linkedStudentRollNo)
                <div><strong>Student ID:</strong> {{ $linkedStudentRollNo }}</div>
            @endif
            @if($relationship)
                <div><strong>Relationship:</strong> {{ $relationship }}</div>
            @endif
        </div>
    </div>
    @endif

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
    <p style="margin-top: 30px; font-weight: 600; color: #1f2937;">Parent Portal Features:</p>
    <ul class="feature-list">
        <li>Track your child's daily attendance records</li>
        <li>View examination schedules and results</li>
        <li>Monitor academic performance and grades</li>
        <li>Receive important notices and announcements</li>
        <li>Access study materials and course information</li>
        <li>Stay connected with the IT Department</li>
        <li>Review your child's progress reports</li>
    </ul>

    <!-- Security Warning -->
    <div class="security-note">
        <strong>⚠️ Important Security Notice:</strong> Please change your temporary password immediately after your first login. Use a strong, unique password to protect your account and your child's information.
    </div>

    <!-- Additional Info -->
    <div class="info-box">
        <strong>Need Support?</strong> If you have questions or need assistance accessing your account, please contact the IT Department or your child's class teacher.
    </div>

    <p style="margin-top: 30px; color: #6b7280;">Best regards,<br><strong>IT Department</strong><br>Department of Computer Science & Engineering</p>
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

