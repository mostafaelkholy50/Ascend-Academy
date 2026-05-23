<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 - طلبات كثيرة جداً | Ascend Academy</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <style>
        :root {
            --bg-gradient: linear-gradient(180deg, #009FBC 0%, #f8fafc 100%);
            --card-bg: rgba(255, 255, 255, 0.9);
            --card-border: rgba(255, 255, 255, 0.6);
            --primary: #009FBC;
            --primary-hover: #1E90A0;
            --text-main: #0f172a;
            --text-muted: #475569;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', 'Inter', sans-serif;
            background: var(--bg-gradient);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow-x: hidden;
            position: relative;
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 550px;
            z-index: 10;
            text-align: center;
        }

        /* Brand Header */
        .brand-header {
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            height: 80px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.05));
            animation: bounceSoft 4s ease-in-out infinite;
        }

        @keyframes bounceSoft {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .brand-title span {
            color: #ffd700;
        }

        /* Error Card */
        .error-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 40px 32px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 40px rgba(0, 159, 188, 0.15),
                        0 1px 3px rgba(0, 0, 0, 0.05);
            animation: cardAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardAppear {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Icon Wrapper */
        .error-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 159, 188, 0.1);
            border-radius: 50%;
            color: var(--primary);
            font-size: 40px;
            animation: pulseLight 2s infinite alternate;
        }

        @keyframes pulseLight {
            0% { box-shadow: 0 0 0 0 rgba(0, 159, 188, 0.2); }
            100% { box-shadow: 0 0 0 15px rgba(0, 159, 188, 0); }
        }

        /* Status Code */
        .status-code {
            font-size: 64px;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 8px;
            font-family: 'Inter', sans-serif;
            letter-spacing: -1px;
        }

        /* Text Content */
        h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 12px;
        }

        p {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 28px;
            max-width: 440px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Buttons */
        .btn-container {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 28px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            outline: none;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 159, 188, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 159, 188, 0.4);
        }

        .btn-secondary {
            background: rgba(0, 0, 0, 0.03);
            color: var(--text-main);
            border: 1px solid rgba(0, 0, 0, 0.08);
        }

        .btn-secondary:hover {
            background: rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        /* Language subtext */
        .lang-text {
            display: block;
            font-size: 13px;
            color: #64748b;
            margin-top: 24px;
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo / Identity Header -->
        <div class="brand-header">
            <a href="/">
                <img src="{{ asset('assets/images/Gemini_Generated_Image_pez0qlpez0qlpez0.png') }}" alt="Ascend Quran Academy Logo" class="brand-logo">
            </a>
            <h2 class="brand-title">Ascend <span>Qur’an</span> Academy</h2>
        </div>

        <!-- Error Card -->
        <div class="error-card">
            <div class="error-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <div class="status-code">429</div>

            <h1>طلبات كثيرة جداً</h1>
            
            <p>
                لقد قمت بإرسال الكثير من الطلبات في فترة زمنية قصيرة. حفاظاً على أمان خوادمنا وحماية حسابك من الاختراق، يرجى الانتظار بضع دقائق ثم المحاولة مجدداً.
            </p>

            <div class="btn-container">
                <a href="javascript:history.back()" class="btn btn-primary">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>المحاولة مجدداً</span>
                </a>
                <a href="/" class="btn btn-secondary">
                    <i class="fa-solid fa-house"></i>
                    <span>العودة للرئيسية</span>
                </a>
            </div>

            <span class="lang-text">Too Many Requests. Please wait a few minutes before trying again.</span>
        </div>
    </div>
</body>
</html>
