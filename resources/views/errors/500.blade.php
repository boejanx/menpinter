<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
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
            background: #fff;
            padding: 32px 28px 28px 28px;
            border-radius: 14px;
            box-shadow: 0 2px 16px 0 rgba(50,50,93,0.08), 0 1.5px 6px 0 rgba(0,0,0,0.02);
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
            <svg width="90" height="90" viewBox="0 0 90 90" fill="none">
                <rect x="10" y="40" width="70" height="30" rx="8" fill="#e6ecfa"/>
                <circle cx="45" cy="38" r="18" fill="#d0dbf7"/>
                <ellipse cx="45" cy="75" rx="18" ry="4" fill="#f6f7fb"/>
                <rect x="41" y="49" width="8" height="13" rx="4" fill="#b4c7ed"/>
                <rect x="20" y="62" width="50" height="3" rx="1.5" fill="#b4c7ed"/>
            </svg>
        </div>
        <h1>500</h1>
        <p>Error</p>
        <a href="/">Kembali ke Beranda</a>
    </div>
</body>
</html>