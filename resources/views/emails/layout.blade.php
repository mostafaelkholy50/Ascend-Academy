<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ascend Academy')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f4f7fa;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-top: 8px;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .content {
            color: #4a5568;
            font-size: 15px;
            line-height: 1.8;
        }
        
        .content p {
            margin-bottom: 16px;
        }
        
        .info-box {
            background: linear-gradient(135deg, #f6f8fb 0%, #e9ecef 100%);
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 24px 0;
            border-radius: 8px;
        }
        
        .info-box h3 {
            color: #2d3748;
            font-size: 16px;
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .info-box p {
            margin-bottom: 8px;
            color: #4a5568;
        }
        
        .info-box strong {
            color: #2d3748;
            font-weight: 600;
        }
        
        .button {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }
        
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 16px;
            margin: 20px 0;
            border-radius: 8px;
            color: #856404;
        }
        
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 16px;
            margin: 20px 0;
            border-radius: 8px;
            color: #155724;
        }
        
        .email-footer {
            background-color: #2d3748;
            padding: 30px;
            text-align: center;
            color: #a0aec0;
            font-size: 13px;
        }
        
        .email-footer p {
            margin-bottom: 8px;
        }
        
        .email-footer a {
            color: #667eea;
            text-decoration: none;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
            margin: 24px 0;
        }
        
        @media only screen and (max-width: 600px) {
            .email-header {
                padding: 30px 20px;
            }
            
            .email-header h1 {
                font-size: 24px;
            }
            
            .email-body {
                padding: 30px 20px;
            }
            
            .button {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <h1>@yield('header', 'Ascend Academy')</h1>
            <p>@yield('subheader', 'Excellence in Islamic Education')</p>
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p><strong>Ascend Academy</strong></p>
            <p>Excellence in Islamic Education</p>
            <div class="divider"></div>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Ascend Academy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
