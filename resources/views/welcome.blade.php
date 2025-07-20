<!DOCTYPE html>
<html>
<head>
    <title>Bienvenue sur MboaSignal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #0a3d62 0%, #1e90ff 100%);
            color: #fff;
            text-align: center;
        }
        .container {
            max-width: 800px;
            padding: 2rem;
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: #fff;
            color: #0a3d62;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur MboaSignal</h1>
        <p>Votre plateforme de signalement citoyen</p>
        @auth
            <a href="{{ route('home') }}" class="btn">Accéder au tableau de bord</a>
        @else
            <a href="{{ route('login') }}" class="btn">Se connecter</a>
        @endauth
    </div>
</body>
</html>
