<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>キャラクター管理</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="container">
        @yield('content')
    </div>

    <!-- スクリプトをここに追加 -->
    @stack('scripts')

    @push('scripts')
        <script>
            // 上記のJavaScriptをここに記載
        </script>
    @endpush

</body>

</html>