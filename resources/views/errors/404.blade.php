<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tersesat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            background: #f6f7fb;
            color: #5a6372;
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .container {
            padding: 32px 28px 28px 28px;
            text-align: center;
            min-width: 320px;
        }
        .illustration {
            margin-bottom: 18px;
        }
        h1 {
            font-size: 42px;
            margin: 6px 0 10px 0;
            font-weight: 400;
            letter-spacing: 1px;
            color: #86a8e7;
        }
        p {
            font-size: 16px;
            margin: 0 0 18px 0;
            color: #7b869c;
        }
        a {
            display: inline-block;
            padding: 8px 22px;
            background: #e6ecfa;
            color: #6473b6;
            border-radius: 7px;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.2s;
        }
        a:hover {
            background: #d0dbf7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="illustration">
            <!-- Ilustrasi SVG Minimalis -->
            <img src="{{asset('assets/img/logo/logo.gif')}}" alt="">
        </div>
        <h1>404</h1>
        <p>Halaman tidak ditemukan.<br>
           Anda tersesat oh tersesat.</p>
        <a href="/">Kembali pulang</a>
    </div>
</body>
</html>