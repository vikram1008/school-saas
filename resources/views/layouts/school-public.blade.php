<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $schoolName ?? config('app.name') }} — Official School Website">
    <title>@yield('title', $schoolName ?? 'School Website')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 (via Vuexy) --}}
    @vite(['resources/assets/vendor/scss/core.scss'])

    {{-- Page styles --}}
    @yield('page-style')

    <style>
        :root {
            --primary:   #1B3F7A;
            --primary-dark: #122b57;
            --accent:    #E8911A;
            --accent-dark: #c97a10;
            --light-bg:  #F5F7FB;
            --card-bg:   #FFFFFF;
            --text-main: #1C2B3A;
            --text-muted:#6B7C93;
            --border:    #E2E8F0;
            --font-display: 'Playfair Display', Georgia, serif;
            --font-body:    'DM Sans', sans-serif;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: var(--font-body);
            color: var(--text-main);
            background: #fff;
            font-size: 15px;
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
        }
        h1,h2,h3,h4,h5 { font-family: var(--font-display); }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; height: auto; display: block; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        /* Back-to-top */
        #back-to-top {
            position: fixed; bottom: 24px; right: 24px; z-index: 999;
            width: 42px; height: 42px; background: var(--accent); color: #fff;
            border: none; border-radius: 50%; cursor: pointer;
            display: none; align-items: center; justify-content: center;
            font-size: 18px; transition: background .2s, transform .2s;
        }
        #back-to-top:hover { background: var(--accent-dark); transform: translateY(-2px); }
        #back-to-top.visible { display: flex; }
    </style>

    @yield('extra-style')
</head>
<body>

    @yield('content')

    <button id="back-to-top" aria-label="Back to top">
        <i class="icon-base ti tabler-arrow-up"></i>
    </button>

    {{-- Bootstrap JS --}}
    @vite(['resources/assets/vendor/js/bootstrap.js'])

    @yield('page-script')

    <script>
        const btn = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => {
            btn.classList.toggle('visible', window.scrollY > 400);
        });
        btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    </script>
</body>
</html>