// Configuration de base de l'API
const API_BASE_URL = '/api/v1';

// Récupérer le jeton CSRF du meta tag
function getCSRFToken() {
    return document.head.querySelector('meta[name="csrf-token"]')?.content;
}

// Fonction utilitaire pour les appels API
async function fetchAPI(endpoint, method = 'GET', data = null, token = null) {
    const url = `${API_BASE_URL}${endpoint}`;
    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    // Ajouter le token CSRF pour les méthodes non-GET
    if (method !== 'GET' && method !== 'HEAD') {
        headers['X-CSRF-TOKEN'] = getCSRFToken();
    }

    // Ajouter le token d'authentification s'il est fourni
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const config = {
        method,
        headers,
        credentials: 'same-origin', // Important pour les cookies de session
    };

    if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
        config.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, config);
        const responseData = await response.json();

        if (!response.ok) {
            throw new Error(responseData.message || 'Une erreur est survenue');
        }

        return responseData;
    } catch (error) {
        console.error('Erreur API:', error);
        throw error;
    }
}

// Gestion de l'authentification
const auth = {
    // Se connecter
    async login(email, password) {
        try {
            const response = await fetchAPI('/login', 'POST', {
                email_utilisateur: email,
                password: password
            });

            // Stocker le token et les informations utilisateur
            if (response.status === 'success' && response.data) {
                const user = response.data.user;
                localStorage.setItem('auth_token', response.data.access_token);
                localStorage.setItem('user', JSON.stringify(user));
                
                // Rediriger en fonction du type d'utilisateur (1=Super Admin, 2=Admin, 3=Technicien)
                const userType = parseInt(user.type_utilisateur);
                switch(userType) {
                    case 1: // Super Admin
                        window.location.href = '/super-admin/dashboard';
                        break;
                    case 2: // Admin
                        window.location.href = '/admin/dashboard';
                        break;
                    case 3: // Technicien
                        window.location.href = '/technicien/dashboard';
                        break;
                    default:
                        window.location.href = '/dashboard';
                }
            } else {
                throw new Error(response.message || 'Erreur lors de la connexion');
            }
            
            return response;
        } catch (error) {
            console.error('Erreur de connexion:', error);
            throw error;
        }
    },

    // Se déconnecter
    async logout() {
        try {
            const token = localStorage.getItem('auth_token');
            await fetchAPI('/logout', 'POST', {}, token);
        } catch (error) {
            console.error('Erreur lors de la déconnexion:', error);
        } finally {
            // Supprimer les données d'authentification
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user');
            window.location.href = '/login';
        }
    },

    // Vérifier si l'utilisateur est connecté
    isAuthenticated() {
        return !!localStorage.getItem('auth_token');
    },

    // Récupérer le token d'authentification
    getToken() {
        return localStorage.getItem('auth_token');
    },

    // Récupérer les informations de l'utilisateur connecté
    getCurrentUser() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    }
};

// Gestion des signalements
const signalements = {
    // Récupérer tous les signalements
    async getAll() {
        return await fetchAPI('/signalements', 'GET', null, auth.getToken());
    },

    // Créer un nouveau signalement
    async create(signalementData) {
        return await fetchAPI('/signalements', 'POST', signalementData, auth.getToken());
    },

    // Récupérer un signalement par son ID
    async getById(id) {
        return await fetchAPI(`/signalements/${id}`, 'GET', null, auth.getToken());
    },

    // Mettre à jour un signalement
    async update(id, signalementData) {
        return await fetchAPI(`/signalements/${id}`, 'PUT', signalementData, auth.getToken());
    },

    // Supprimer un signalement
    async delete(id) {
        return await fetchAPI(`/signalements/${id}`, 'DELETE', null, auth.getToken());
    }
};

// Gestion des utilisateurs (pour les administrateurs)
const users = {
    // Récupérer tous les utilisateurs
    async getAll() {
        return await fetchAPI('/utilisateurs', 'GET', null, auth.getToken());
    },

    // Créer un nouvel utilisateur
    async create(userData) {
        return await fetchAPI('/utilisateurs', 'POST', userData, auth.getToken());
    },

    // Récupérer un utilisateur par son ID
    async getById(id) {
        return await fetchAPI(`/utilisateurs/${id}`, 'GET', null, auth.getToken());
    },

    // Mettre à jour un utilisateur
    async update(id, userData) {
        return await fetchAPI(`/utilisateurs/${id}`, 'PUT', userData, auth.getToken());
    },

    // Supprimer un utilisateur
    async delete(id) {
        return await fetchAPI(`/utilisateurs/${id}`, 'DELETE', null, auth.getToken());
    }
};

// Exposer les fonctions globalement
window.API = {
    fetch: fetchAPI,
    auth,
    signalements,
    users
};

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier l'authentification pour les pages protégées
    const protectedRoutes = ['/dashboard', '/admin', '/super-admin'];
    const currentPath = window.location.pathname;
    
    if (protectedRoutes.some(route => currentPath.startsWith(route)) && !auth.isAuthenticated()) {
        window.location.href = '/login';
        return;
    }

    // Initialisation des écouteurs d'événements pour les formulaires
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }

    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', auth.logout);
    }
});

// Gestion de la soumission du formulaire de connexion
async function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorElement = document.getElementById('login-error');
    
    try {
        await auth.login(email, password);
    } catch (error) {
        errorElement.textContent = error.message || 'Identifiants incorrects';
        errorElement.style.display = 'block';
    }
}

// Fonction utilitaire pour afficher les notifications
function showNotification(message, type = 'success') {
    // Implémentez votre propre système de notification ici
    console.log(`[${type}] ${message}`);
    alert(`[${type}] ${message}`);
}
