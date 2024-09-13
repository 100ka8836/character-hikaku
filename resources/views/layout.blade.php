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
            width: 100%;
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
            z-index: 1000;
        }

        .navbar a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            display: inline-block;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        /* コンテンツをナビゲーションバーの下に配置するための調整 */
        body {
            padding-top: 60px;
            /* ナビゲーションバーの高さに合わせて調整 */
        }
    </style>
</head>

<body>

    <!-- ナビゲーションバー -->
    <div class="navbar">
        <a href="{{ route('characters.self') }}">自分のキャラクター</a>
        <a href="{{ route('characters.friends') }}">友達のキャラクター</a>
        <a href="{{ route('characters.jin') }}">陣ごと</a>
    </div>

    <div class="container">
        @yield('content')
    </div>

    <!-- 必要なスクリプト -->
    @stack('scripts')

</body>

</html>