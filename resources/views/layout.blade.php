<!-- layout.blade.php -->
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'キャラクター管理')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* トップに固定するスタイル */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            /* 左端に固定 */
            width: 100%;
            background-color: #333;
            color: white;
            text-align: center;
            z-index: 1000;
        }

        .navbar a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        /* アクティブなリンクのスタイル */
        .navbar a.active {
            background-color: #ffffff;
            color: #333;
        }

        /* コンテンツをナビゲーションバーの下に配置するための調整 */
        body {
            padding-top: 60px;
            /* ナビゲーションバーの高さに合わせて調整 */
            margin: 0;
            /* 左右の余白をリセット */
        }
    </style>
</head>

<body style="font-family: Kosugi, Arial;">

    <!-- ナビゲーションバー -->
    <div class="navbar">
        <a href="{{ route('characters.self') }}"
            class="{{ request()->routeIs('characters.self') ? 'active' : '' }}">自分のキャラクター</a>
        <a href="{{ route('friend_characters.index') }}">友達のキャラクター一覧</a>
        <a href="{{ route('characters.jin') }}"
            class="{{ request()->routeIs('characters.jin') ? 'active' : '' }}">陣ごと</a>
    </div>

    <!-- コンテンツ -->
    <div class="container">
        @yield('content')
    </div>

    <!-- 必要なスクリプト -->
    @stack('scripts')

</body>

</html>