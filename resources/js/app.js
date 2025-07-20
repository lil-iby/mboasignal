import './bootstrap';
import './http';

// Importer les fichiers spécifiques aux pages
if (document.getElementById('login-form')) {
    import('./login');
}
