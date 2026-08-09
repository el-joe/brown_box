<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:24px; color:#1e293b;" @if(($locale ?? current_lang()) === 'ar') dir="rtl" @endif>
    <div style="max-width:560px; margin:0 auto; background:#fff; border-radius:12px; padding:32px; border:1px solid #e2e8f0;">
        <div style="text-align:center; margin-bottom:24px;">
            @if (setting('site_logo'))
                <img src="{{ asset_url(setting('site_logo')) }}" alt="{{ config('app.name') }}" style="max-height:40px;">
            @else
                <span style="font-size:20px; font-weight:bold; color:#1e293b;">{{ config('app.name') }}</span>
            @endif
        </div>

        @yield('content')

        <hr style="border:none; border-top:1px solid #e2e8f0; margin:24px 0;">
        <p style="color:#94a3b8; font-size:12px; margin:0;">{{ __('Thank you for shopping with :app.', ['app' => config('app.name')]) }}</p>
        <p style="color:#cbd5e1; font-size:11px; margin:8px 0 0;">{{ __('If you no longer wish to receive these emails, please contact support.') }}</p>
    </div>
</body>
</html>
