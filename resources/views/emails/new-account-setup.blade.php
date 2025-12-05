<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MediCare</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
        }
        .logo span {
            color: #10b981;
        }
        h1 {
            color: #1f2937;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-doctor {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .role-patient {
            background-color: #d1fae5;
            color: #065f46;
        }
        .credentials-box {
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials-box h3 {
            margin-top: 0;
            color: #374151;
        }
        .credential-item {
            margin: 10px 0;
        }
        .credential-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 12px;
            text-transform: uppercase;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            color: #111827;
            background-color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            display: block;
            margin-top: 5px;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        .warning strong {
            color: #92400e;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            margin: 10px 5px;
        }
        .btn-primary {
            background-color: #3b82f6;
            color: #ffffff;
        }
        .btn-secondary {
            background-color: #6b7280;
            color: #ffffff;
        }
        .buttons {
            text-align: center;
            margin: 25px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .steps {
            margin: 20px 0;
        }
        .step {
            display: flex;
            align-items: flex-start;
            margin: 15px 0;
        }
        .step-number {
            background-color: #3b82f6;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .step-content {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Medi<span>Care</span></div>
            <p style="color: #6b7280; margin: 5px 0;">Medical Clinic Management System</p>
        </div>

        <h1>Welcome to MediCare! 🎉</h1>
        
        <p>Hello <strong>{{ $user->name }}</strong>,</p>
        
        <p>An account has been created for you as a 
            <span class="role-badge role-{{ $role }}">{{ ucfirst($role) }}</span>
            on our medical clinic system.
        </p>

        <div class="credentials-box">
            <h3>🔐 Your Login Credentials</h3>
            <div class="credential-item">
                <div class="credential-label">Email Address</div>
                <div class="credential-value">{{ $user->email }}</div>
            </div>
            <div class="credential-item">
                <div class="credential-label">Temporary Password</div>
                <div class="credential-value">{{ $temporaryPassword }}</div>
            </div>
        </div>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong><br>
            This is a temporary password. For your security, we strongly recommend changing it immediately after your first login.
        </div>

        <h3>📋 Getting Started</h3>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <strong>Log in</strong> using the credentials above
                </div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <strong>Change your password</strong> from your profile settings
                </div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    @if($role === 'doctor')
                    <strong>Set up your availability</strong> to start receiving appointments
                    @else
                    <strong>Book your first appointment</strong> with one of our specialists
                    @endif
                </div>
            </div>
        </div>

        <div class="buttons">
            <a href="{{ $loginUrl }}" class="btn btn-primary">Login Now</a>
            <a href="{{ $resetUrl }}" class="btn btn-secondary">Reset Password</a>
        </div>

        <div class="footer">
            <p>If you didn't expect this email or have any questions, please contact our support team.</p>
            <p><strong>MediCare Medical Clinic</strong><br>
            © {{ date('Y') }} All rights reserved.</p>
        </div>
    </div>
</body>
</html>
