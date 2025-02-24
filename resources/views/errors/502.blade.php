<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>502 - Bad Gateway</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        body {
            margin: 0;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Inter', sans-serif;
            color: #fff;
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.15);
            padding: 2rem;
            border-radius: 1rem;
            backdrop-filter: blur(10px);
        }

        .container i {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        h1 {
            font-size: 4rem;
            margin: 0;
        }

        p {
            font-size: 1.5rem;
            margin: 1rem 0 0;
        }

        a {
            display: inline-block;
            margin-top: 2rem;
            background: #ffffff;
            color: #2563eb;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            background: #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="container">
        <i class="fa-solid fa-cloud-bolt"></i>
        <h1>502</h1>
        <p>Bad Gateway<br>Maaf, terjadi kesalahan pada server.</p>
        <a href="/">Kembali ke Beranda</a>
    </div>
</body>

</html>
