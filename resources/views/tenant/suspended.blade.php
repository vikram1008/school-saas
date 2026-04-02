<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Suspended — {{ $saas_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .suspended-card {
            max-width: 520px;
            width: 100%;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fff3f3;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="suspended-card text-center p-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <div class="icon-circle">
                    🔒
                </div>
                <h3 class="fw-bold text-danger mb-2">Account Suspended</h3>
                <p class="text-muted mb-1">
                    <strong>{{ $school->school_name }}</strong>
                </p>
                <p class="text-muted mb-4">
                    Access to this account has been suspended due to an overdue payment.
                    All your data is safe and will be restored immediately upon reactivation.
                </p>
                <div class="alert alert-warning text-start mb-4">
                    <strong>To reactivate your account:</strong><br>
                    Please contact your platform administrator at<br>
                    <a href="mailto:{{ $support_email }}" class="fw-semibold">
                        {{ $support_email }}
                    </a>
                </div>
                <p class="text-muted small mb-0">
                    {{ $saas_name }} &mdash; School Management Platform
                </p>
            </div>
        </div>
    </div>
</body>
</html>