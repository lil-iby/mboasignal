// Fonction pour gérer la connexion
export const login = async (email, password) => {
    try {
        const response = await fetch('/api/v1/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                email_utilisateur: email,
                password: password
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Stocker le token et les informations utilisateur
            localStorage.setItem('auth_token', data.data.access_token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            
            // Rediriger en fonction du type d'utilisateur
            redirectBasedOnUserType(data.data.user.type_utilisateur);
            
            return { success: true, data: data.data };
        } else {
            throw new Error(data.message || 'Échec de la connexion');
        }
    } catch (error) {
        console.error('Erreur de connexion:', error);
        return { success: false, error: error.message };
    }
};

// Fonction pour rediriger en fonction du type d'utilisateur
function redirectBasedOnUserType(userType) {
    switch(parseInt(userType)) {
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
            // Redirection par défaut si le type n'est pas reconnu
            window.location.href = '/dashboard';
    }
}

// Fonction pour vérifier si l'utilisateur est connecté
export const isAuthenticated = () => {
    return !!localStorage.getItem('auth_token');
};

// Fonction pour récupérer l'utilisateur connecté
export const getCurrentUser = () => {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
};

// Fonction pour se déconnecter
export const logout = () => {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    window.location.href = '/login';
};
