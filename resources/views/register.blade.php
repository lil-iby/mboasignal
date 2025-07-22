<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Inscription - MboaSignal</title>

  <!-- Google Fonts Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet" />
  
  <style>
    /* Styles de base identiques à la page de connexion */
    *, *::before, *::after { box-sizing: border-box; }
    
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #0a3d62 0%, #1e90ff 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #f0f4f8;
      overflow-y: auto;
      padding: 2rem 0;
    }

    .register-wrapper {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 2.5rem 3rem;
      width: 100%;
      max-width: 500px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.25);
      color: #1b2a49;
      margin: 2rem 1rem;
    }

    .logo {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .logo svg {
      width: 60px;
      height: 60px;
      filter: drop-shadow(0 0 6px rgba(30,144,255,0.7));
    }

    h1 {
      font-size: 2rem;
      margin-bottom: 1.5rem;
      text-align: center;
      color: #fff;
      text-shadow: 0 0 6px rgba(30,144,255,0.7);
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #d1d9e6;
      font-size: 0.9rem;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="tel"],
    select {
      width: 100%;
      padding: 0.8rem 1rem;
      font-size: 1rem;
      border-radius: 8px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      background: rgba(255, 255, 255, 0.9);
      transition: all 0.3s ease;
    }

    input:focus, select:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(30, 144, 255, 0.3);
      background: #fff;
    }

    .form-row {
      display: flex;
      gap: 1rem;
      margin-bottom: 1.2rem;
    }

    .form-row .form-group {
      flex: 1;
      margin-bottom: 0;
    }

    button[type="submit"] {
      width: 100%;
      padding: 0.9rem;
      background: linear-gradient(90deg, #1e90ff, #0a3d62);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 0.5rem;
    }

    button[type="submit"]:hover {
      background: linear-gradient(90deg, #0a3d62, #1e90ff);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .login-link {
      text-align: center;
      margin-top: 1.5rem;
      color: #d1d9e6;
    }

    .login-link a {
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    .login-link a:hover {
      color: #1e90ff;
      text-decoration: underline;
    }

    .error-message {
      color: #ff6b6b;
      background: rgba(255, 107, 107, 0.1);
      padding: 0.8rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      display: none;
    }

    .success-message {
      color: #28a745;
      background: rgba(40, 167, 69, 0.1);
      padding: 0.8rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      display: none;
    }

    @media (max-width: 576px) {
      .register-wrapper {
        padding: 2rem 1.5rem;
        margin: 1rem;
      }
      
      .form-row {
        flex-direction: column;
        gap: 1.2rem;
      }
    }
  </style>
</head>
<body>
  <div class="register-wrapper">
    <div class="logo">
      <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="32" cy="32" r="30" stroke="#1E90FF" stroke-width="4"/>
        <path d="M20 32L28 40L44 24" stroke="#1E90FF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    
    <h1>Créer un compte</h1>
    
    <div id="error-message" class="error-message"></div>
    <div id="success-message" class="success-message"></div>
    
    <form id="register-form">
      <div class="form-row">
        <div class="form-group">
          <label for="nom">Nom *</label>
          <input type="text" id="nom" name="nom_utilisateur" required>
        </div>
        <div class="form-group">
          <label for="prenom">Prénom *</label>
          <input type="text" id="prenom" name="prenom_utilisateur" required>
        </div>
      </div>
      
      <div class="form-group">
        <label for="email">Adresse email *</label>
        <input type="email" id="email" name="email_utilisateur" required>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="password">Mot de passe *</label>
          <input type="password" id="password" name="pass_utilisateur" required minlength="8">
        </div>
        <div class="form-group">
          <label for="password_confirmation">Confirmer le mot de passe *</label>
          <input type="password" id="password_confirmation" name="pass_utilisateur_confirmation" required>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="telephone">Téléphone *</label>
          <input type="tel" id="telephone" name="tel_utilisateur" required>
        </div>
        <div class="form-group">
          <label for="type_utilisateur">Type de compte *</label>
          <select id="type_utilisateur" name="type_utilisateur" required>
            <option value="utilisateur">Utilisateur</option>
            <option value="admin">Administrateur</option>
          </select>
        </div>
      </div>
      
      <button type="submit" id="register-button">
        <span class="button-text">S'inscrire</span>
        <span class="button-loader" style="display: none;">Création du compte...</span>
      </button>
    </form>
    
    <div class="login-link">
      Déjà un compte ? <a href="{{ route('login') }}">Connectez-vous ici</a>
    </div>
  </div>

  <!-- Inclure le fichier API -->
  <script src="{{ asset('js/api.js') }}"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('register-form');
      const errorMessage = document.getElementById('error-message');
      const successMessage = document.getElementById('success-message');
      const registerButton = document.getElementById('register-button');
      const buttonText = registerButton.querySelector('.button-text');
      const buttonLoader = registerButton.querySelector('.button-loader');

      form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Réinitialiser les messages
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';
        
        // Vérifier que les mots de passe correspondent
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;
        
        if (password !== passwordConfirm) {
          errorMessage.textContent = 'Les mots de passe ne correspondent pas';
          errorMessage.style.display = 'block';
          return;
        }
        
        // Préparer les données du formulaire
        const formData = {
          nom_utilisateur: document.getElementById('nom').value,
          prenom_utilisateur: document.getElementById('prenom').value,
          email_utilisateur: document.getElementById('email').value,
          pass_utilisateur: password,
          pass_utilisateur_confirmation: passwordConfirm,
          tel_utilisateur: document.getElementById('telephone').value,
          type_utilisateur: document.getElementById('type_utilisateur').value
        };
        
        // Désactiver le bouton pendant la requête
        registerButton.disabled = true;
        buttonText.style.display = 'none';
        buttonLoader.style.display = 'inline';
        
        try {
          // Envoyer la requête d'inscription
          const response = await window.API.fetch('/register', 'POST', formData);
          
          // Afficher le message de succès
          successMessage.textContent = 'Compte créé avec succès ! Redirection...';
          successMessage.style.display = 'block';
          
          // Rediriger vers la page de connexion après un court délai
          setTimeout(() => {
            window.location.href = '{{ route("login") }}';
          }, 2000);
          
        } catch (error) {
          console.error('Erreur lors de l\'inscription:', error);
          
          // Afficher le 
          errorMessage.textContent = error.message || 'Une erreur est survenue lors de l\'inscription';
          errorMessage.style.display = 'block';
          
          // Rejeter l'erreur pour une éventuelle gestion supplémentaire
          throw error;
          
        } finally {
          // Réactiver le bouton dans tous les cas
          registerButton.disabled = false;
          buttonText.style.display = 'inline';
          buttonLoader.style.display = 'none';
        }
      });
    });
  </script>
</body>
</html>
