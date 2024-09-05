<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Financial Management')</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Vazir:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&display=swap">
</head>
<body>
    <header>
        <h1>Financial Management</h1>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} Financial Management</p>
    </footer>
</body>
</html>