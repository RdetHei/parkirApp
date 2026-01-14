<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <title>Parkir App - Landing Page</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            body {
                font-family: 'Figtree', sans-serif;
                background-color: #f0f2f5;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                color: #333;
            }
            .container {
                text-align: center;
                background-color: #fff;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                max-width: 600px;
                width: 90%;
            }
            h1 {
                font-size: 2.5em;
                color: #3490dc;
                margin-bottom: 20px;
            }
            p {
                font-size: 1.1em;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            .buttons a {
                display: inline-block;
                background-color: #3490dc;
                color: #fff;
                padding: 12px 25px;
                border-radius: 5px;
                text-decoration: none;
                font-weight: bold;
                margin: 10px;
                transition: background-color 0.3s ease;
            }
            .buttons a:hover {
                background-color: #2779bd;
            }
            .buttons a.secondary {
                background-color: #6cb2eb;
            }
            .buttons a.secondary:hover {
                background-color: #559bdc;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Selamat Datang di Parkir App</h1>
            <p>Solusi mudah untuk menemukan dan memesan tempat parkir. Nikmati kenyamanan parkir tanpa stres!</p>
            <div class="buttons">
        
            </div>
        </div>
    </body>
</html>
