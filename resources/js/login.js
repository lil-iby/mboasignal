import { login } from './auth';

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorElement = document.getElementById('login-error');
            const submitButton = loginForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            try {
                // Désactiver le bouton et afficher un indicateur de chargement
                submitButton.disabled = true;
                submitButton.innerHTML = 'Connexion en cours...';
                
                const result = await login(email, password);
                
                if (!result.success) {
                    throw new Error(result.error || 'Une erreur est survenue lors de la connexion');
                }
                
                // La redirection est gérée dans la fonction login()
                
            } catch (error) {
                console.error('Erreur de connexion:', error);
                errorElement.textContent = error.message || 'Identifiants incorrects';
                errorElement.style.display = 'block';
                
                // Réactiver le bouton
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }
});
