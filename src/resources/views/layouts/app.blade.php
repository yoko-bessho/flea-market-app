<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'COACHTECH')</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
            <span class="header__logo">COACHTECH</span>
            <form action="" class="search-form" method="get">
                <span class="search-form__item">
                    <input class="search-form__item-input" type="text" value="何をお探しですか？"/>
                </span>
            </form>
            @yield('header')
            </div>
        </div>
    </header>

    <main>
      @yield('content')
    </main>

</body>
</html>
