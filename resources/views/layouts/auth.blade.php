<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Log In') - BuildRight Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-logo"><i class="bi bi-tools"></i></div>
                <h1>BuildRight Hardware</h1>
                <p>Inventory Management System</p>
            </div>

            @yield('content')
        </div>
        <p class="auth-footer">&copy; {{ date('Y') }} BuildRight Hardware</p>
    </div>

    @yield('scripts')
</body>
</html>
