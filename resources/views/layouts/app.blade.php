<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMKM SIAP - Ekspor Produk Indonesia ke Dunia Global</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --green: #1a6b55;
            --green-dark: #0B6E5E;
            --green-darkmore: #084D42;
            --green-light: #e8f5f0;
            --yellow-dark: #C49200;
            --yellow-light: #F5B903;
            --yellow-soft: #FFF8E0;
            --text-black: #1a1a1a;
            --text-muted: #555;
            --white: #ffffff;
            --gray-bg: #f4f4f4;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background: var(--white);
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
    @stack('styles')
</head>

<body>
    @yield('content')
    @stack('scripts')
</body>

</html>
