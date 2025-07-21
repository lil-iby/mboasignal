<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0a3d62 0%, #1e90ff 100%);
            font-family: 'Poppins', sans-serif;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .unauthorized-container {
            background: rgba(255,255,255,0.10);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            border-radius: 18px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        .unauthorized-container h1 {
            font-size: 2.2rem;
            margin-bottom: 1.2rem;
            color: #ff6b6b;
        }
        .unauthorized-container p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .unauthorized-container button {
            background: linear-gradient(90deg, #1e90ff, #0a3d62);
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 12px;
            border: none;
            padding: 0.8rem 2.2rem;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(30,144,255,0.5);
            transition: background 0.3s, transform 0.2s;
        }
        .unauthorized-container button:hover {
            background: linear-gradient(90deg, #0a3d62, #1e90ff);
            transform: scale(1.04);
        }
    </style>
</head>
<body>
    <div class="unauthorized-container">
        <h1>Accès refusé</h1>
        <p>Vous n'avez pas les droits nécessaires pour accéder à cette page.<br>
        Veuillez vous connecter avec un compte autorisé.</p>
        <button onclick="window.location.href='/login'">Retour à la connexion</button>
    </div>
</body>
</html>
