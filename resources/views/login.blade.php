<?php
//todo: implementer la logique PHP pour notre backend
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Connexion - MboaSignal</title>

  <!-- Google Fonts Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body, html {
      margin: 0; padding: 0; height: 100%;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0a3d62 0%, #1e90ff 100%);
      display: flex; align-items: center; justify-content: center;
      color: #f0f4f8; overflow: hidden;
    }

    @keyframes fadeSlideUp {
      0% { opacity: 0; transform: translateY(25px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    .login-wrapper {
      background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(12px);
      border-radius: 16px; padding: 3rem 3.5rem;
      width: 100%; max-width: 420px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.25);
      text-align: center; color: #1b2a49;
      animation: fadeSlideUp 0.7s ease forwards;
    }

    .logo {
      margin-bottom: 1.8rem; animation: fadeSlideUp 0.8s ease forwards;
    }
    .logo svg {
      width: 64px; height: 64px;
      filter: drop-shadow(0 0 6px rgba(30,144,255,0.7));
    }

    h1 {
      font-size: 2.4rem; margin-bottom: 2rem;
      font-weight: 700; letter-spacing: 1.2px;
      color: #fff; text-shadow: 0 0 6px rgba(30,144,255,0.7);
      animation: fadeSlideUp 1s ease forwards;
    }

    form {
      display: flex; flex-direction: column; gap: 1.5rem;
      animation: fadeSlideUp 1.2s ease forwards;
    }

    label {
      font-weight: 600; text-align: left; color: #d1d9e6;
      font-size: 0.9rem; user-select: none;
      animation: fadeSlideUp 1.4s ease forwards;
    }

    input[type="email"],
    input[type="password"] {
      padding: 0.85rem 1.2rem; font-size: 1rem;
      border-radius: 12px; border: none; outline: none;
      background: rgba(255, 255, 255, 0.9);
      box-shadow: inset 2px 2px 6px rgba(0,0,0,0.1);
      animation: fadeSlideUp 1.6s ease forwards;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
      background-color: #fff;
      box-shadow: 0 0 8px 3px #1e90ff;
    }

    button {
      background: linear-gradient(90deg, #1e90ff, #0a3d62);
      color: white; font-weight: 700; font-size: 1.15rem;
      border-radius: 14px; border: none;
      padding: 0.9rem 0; cursor: pointer;
      box-shadow: 0 5px 15px rgba(30,144,255,0.7);
      animation: fadeSlideUp 1.8s ease forwards;
    }

    button:hover, button:focus {
      background: linear-gradient(90deg, #0a3d62, #1e90ff);
      box-shadow: 0 8px 20px rgba(30,144,255,0.9);
      transform: scale(1.05);
    }

    .forgot {
      margin-top: 1rem; font-size: 0.9rem;
      text-align: right; color: #bdd7ff;
      animation: fadeSlideUp 2s ease forwards;
    }

    .forgot a {
      color: #d1d9e6; text-decoration: none; font-weight: 600;
    }

    .forgot a:hover {
      color: #1e90ff; text-decoration: underline;
    }

    @media (max-width: 480px) {
      .login-wrapper { padding: 2rem; margin: 0 1rem; }
      .login-wrapper h1 { font-size: 2rem; }
      .logo svg { width: 48px; height: 48px; }
    }
  </style>
</head>
<body>
  <main class="login-wrapper" role="main" aria-label="Formulaire de connexion">
    <div class="logo" aria-hidden="true">
      <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="30" stroke="#1E90FF" stroke-width="4"/>
        <path d="M20 32L28 40L44 24" stroke="#1E90FF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <h1>Connexion MboaSignal</h1>
    <form id="login-form" method="POST" novalidate>
      @csrf
      <div id="login-error" class="error-message" style="display: none; color: #ff6b6b; margin-bottom: 1rem; font-weight: 500;"></div>

      <label for="email">Adresse e-mail</label>
      <input type="email" id="email" name="email_utilisateur" placeholder="exemple@erinn.com" required autofocus autocomplete="username" />

      <label for="password">Mot de passe</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" />

      <button type="submit" id="login-button" aria-label="Se connecter">
        <span class="button-text">Se connecter</span>
        <span class="button-loader" style="display: none;">Connexion en cours...</span>
      </button>
    </form>

    <div style="margin-top: 1.5rem; text-align: center; color: #d1d9e6; font-size: 0.9rem;">
      <p>Vous n'avez pas de compte ? <a href="{{ route('register') }}" style="color: #fff; font-weight: 600;">S'inscrire</a></p>
      <p class="forgot"><a href="#" tabindex="0">Mot de passe oublié ?</a></p>
    </div>
  </main>

  <script>
    document.getElementById('login-form').addEventListener('submit', async function(e) {
      e.preventDefault();

      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const errorElement = document.getElementById('login-error');
      const buttonText = document.querySelector('#login-button .button-text');
      const buttonLoader = document.querySelector('#login-button .button-loader');
      const loginButton = document.getElementById('login-button');

      loginButton.disabled = true;
      buttonText.style.display = 'none';
      buttonLoader.style.display = 'inline';
      errorElement.style.display = 'none';

      try {
        const response = await fetch('/api/v1/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            email_utilisateur: email,
            pass_utilisateur: password
          })
        });

        const data = await response.json();

        if (!response.ok || data.status !== "success") {
          throw new Error(data.message || 'Erreur de connexion');
        }

        const token = data.data.authorization.token;
        const user = data.data.user;

        // Stocker toutes les infos nécessaires
        localStorage.setItem('auth_token', token);
        localStorage.setItem('user', JSON.stringify(user));
        localStorage.setItem('type_utilisateur', user.type_utilisateur);
        localStorage.setItem('nom_utilisateur', user.nom + ' ' + user.prenom);

        // Redirection selon rôle
        if (user.type_utilisateur === 'superadmin') {
          window.location.href = '/superadmin/dashboard';
        } else if (user.type_utilisateur === 'admin') {
          window.location.href = '/admin/dashboard';
        } else {
          window.location.href = '/unauthorized';
        }

      } catch (error) {
        errorElement.textContent = error.message;
        errorElement.style.display = 'block';
      } finally {
        loginButton.disabled = false;
        buttonText.style.display = 'inline';
        buttonLoader.style.display = 'none';
      }
    });

    document.getElementById('password').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        document.getElementById('login-form').dispatchEvent(new Event('submit'));
      }
    });
  </script>
</body>
</html>
