<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>502 - Bad Gateway</title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-light: #3b82f6;
            --primary-dark: #2563eb;
            --bg-overlay: rgba(255, 255, 255, 0.2);
            --text-dark: #2563eb;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #fff;
        }

        .container {
            background: var(--bg-overlay);
            backdrop-filter: blur(8px);
            border-radius: 1rem;
            padding: 2rem 3rem;
            text-align: center;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            max-width: 500px;
            margin: 0 1rem;
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
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        h2 {
            font-size: 1.5rem;
            margin: 1rem 0;
            font-weight: 600;
        }

        p {
            font-size: 1.2rem;
            margin: 1rem 0;
        }

        a {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.75rem 1.5rem;
            background: #ffffff;
            color: var(--text-dark);
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
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
        <h2>Bad Gateway</h2>
        <p>Maaf, terjadi kesalahan pada server kami.<br>Silakan coba beberapa saat lagi atau kembali ke halaman utama.
        </p>
        <a href="/">Kembali ke Beranda</a>
    </div>
</body>

</html>
