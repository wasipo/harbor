<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - ページが見つかりません</title>
    @vite(['resources/js/app/app.tsx'])
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: #f3f4f6;
        }
        .container {
            text-align: center;
            padding: 2rem;
        }
        h1 {
            font-size: 6rem;
            margin: 0;
            color: #6b7280;
        }
        p {
            font-size: 1.25rem;
            color: #4b5563;
            margin: 1rem 0;
        }
        a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: background 0.2s;
        }
        a:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <p>お探しのページが見つかりません</p>
        <p>ページが移動または削除された可能性があります</p>
        <a href="/dashboard">ダッシュボードに戻る</a>
    </div>
</body>
</html>