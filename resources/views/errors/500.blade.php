<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <style>
        body {
            background-color: #f7fafc;
            color: #4a5568;
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #e53e3e;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #3182ce;
            color: #fff;
            border-radius: 5px;
        }
        a:hover {
            background-color: #2b6cb0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>500</h1>
        <p>Maaf, terjadi kesalahan pada server kami. Silahkan coba beberapa saat lagi atau kembali ke halaman utama.</p>
        <a href="{{ url('/') }}">Kembali ke Home</a>
    </div>
</body>
</html>
