# Documentation de l'API MboaSignal

## Introduction
Cette documentation décrit comment interagir avec l'API RESTful de MboaSignal. L'API permet de gérer les signalements, utilisateurs, catégories, médias, notifications, organismes et visiteurs.

## Base URL
Toutes les requêtes doivent être préfixées par `/api`.

## Authentification
L'API utilise l'authentification par token Bearer.

### S'enregistrer
```http
POST /api/register
```

**Corps de la requête :**
```json
{
  "nom_utilisateur": "Doe",
  "prenom_utilisateur": "John",
  "email_utilisateur": "john@example.com",
  "pass_utilisateur": "motdepasse",
  "pass_utilisateur_confirmation": "motdepasse",
  "type_utilisateur": "utilisateur",
  "tel_utilisateur": "+1234567890"
}
```

### Se connecter
```http
POST /api/login
```

**Corps de la requête :**
```json
{
  "email_utilisateur": "john@example.com",
  "pass_utilisateur": "motdepasse"
}
```

**Réponse :**
```json
{
  "access_token": "token_jwt_ici",
  "token_type": "Bearer",
  "utilisateur": {
    "id": 1,
    "nom_utilisateur": "Doe",
    "email_utilisateur": "john@example.com",
    "type_utilisateur": "utilisateur"
  }
}
```

### Utilisation du token
Inclure le token dans l'en-tête des requêtes :
```
Authorization: Bearer votre_token_ici
```

## Endpoints

### Signalements
- `GET /api/signalements` - Lister tous les signalements
- `POST /api/signalements` - Créer un signalement
- `GET /api/signalements/{id}` - Obtenir un signalement
- `PUT /api/signalements/{id}` - Mettre à jour un signalement
- `DELETE /api/signalements/{id}` - Supprimer un signalement

### Utilisateurs
- `GET /api/utilisateurs` - Lister les utilisateurs
- `GET /api/utilisateurs/{id}` - Obtenir un utilisateur
- `PUT /api/utilisateurs/{id}` - Mettre à jour un utilisateur
- `DELETE /api/utilisateurs/{id}` - Supprimer un utilisateur

### Catégories
- `GET /api/categories` - Lister les catégories
- `POST /api/categories` - Créer une catégorie
- `GET /api/categories/{id}` - Obtenir une catégorie
- `PUT /api/categories/{id}` - Mettre à jour une catégorie
- `DELETE /api/categories/{id}` - Supprimer une catégorie

## Réponses d'erreur
L'API renvoie des codes d'état HTTP appropriés :
- `200` : Requête réussie
- `201` : Ressource créée
- `204` : Pas de contenu (suppression réussie)
- `400` : Mauvaise requête
- `401` : Non autorisé
- `403` : Interdit
- `404` : Non trouvé
- `422` : Erreur de validation
- `500` : Erreur serveur

## Exemple avec cURL

### Créer un signalement
```bash
curl -X POST http://votre-domaine.com/api/signalements \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer votre_token_ici" \
  -d '{
    "titre_signalement": "Nid de poule",
    "description_signalement": "Gros nid de poule sur la route principale",
    "localisation_signalement": "Rue principale, Quartier Central",
    "date_signalement": "2023-01-01",
    "etat_signalement": "nouveau",
    "utilisateur_id": 1,
    "categorie_id": 1
  }'
```

## Sécurité
- Utilisez toujours HTTPS
- Ne partagez jamais votre token d'authentification
- Utilisez des mots de passe forts
- Limitez les tentatives de connexion

## Support
Pour toute question ou problème, veuillez contacter l'équipe de support à support@mboasignal.com.
