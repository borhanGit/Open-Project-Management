<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to OpenProject</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            line-height: 1.6;
        }
        .container {
            max-width: 580px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
            padding: 36px 32px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 14px;
            color: #dbeafe;
        }
        .content {
            padding: 36px 32px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
        }
        .card-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        .card-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .label {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .value {
            font-weight: 600;
            color: #0f172a;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0 24px 0;
        }
        .btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .alert {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 12px 16px;
            border-radius: 6px;
            margin-top: 24px;
            font-size: 13px;
            color: #1e40af;
        }
        .footer {
            padding: 20px 32px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>OpenProject</h1>
            <p>Open Source Project Management Platform</p>
        </div>

        <div class="content">
            <div class="greeting">Hello, {{ $user->name }}!</div>
            <p>An administrator has created an account for you on OpenProject. You can now access your projects, collaborate with your team, and track your work packages.</p>

            <div class="card">
                <div class="card-row">
                    <span class="label">Email</span>
                    <span class="value">{{ $user->email }}</span>
                </div>
                <div class="card-row">
                    <span class="label">Initial Password</span>
                    <span class="value">{{ $plainPassword }}</span>
                </div>
                <div class="card-row">
                    <span class="label">Role</span>
                    <span class="value">{{ $user->isAdmin() ? 'Administrator' : 'Team Member' }}</span>
                </div>
            </div>

            <div class="btn-wrapper">
                <a href="{{ $loginUrl }}" class="btn">Sign In to OpenProject &rarr;</a>
            </div>

            <div class="alert">
                <strong>Important Security Note:</strong> For your security, we recommend signing in and immediately changing your password in your <strong>Profile Settings</strong>.
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} OpenProject. Open source software licensed under MIT.
        </div>
    </div>
</body>
</html>
