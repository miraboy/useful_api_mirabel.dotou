# Frontend Vue 3 + Vite - Dashboard Authentification

## 📋 Description

Mini-dashboard interactif avec système d'authentification complet utilisant l'API backend Laravel.

## 🚀 Fonctionnalités

### ✅ Authentification

-   **Login** : Formulaire avec email et mot de passe
-   **Register** : Formulaire avec nom, email et mot de passe
-   **Token persistant** : Stockage sécurisé dans localStorage via Pinia
-   **Routes protégées** : Navigation guard automatique
-   **Auto-logout** : Bouton de déconnexion

### 🛠️ Technologies

-   **Vue 3** : Framework progressif
-   **Vite** : Build tool ultra-rapide
-   **Pinia** : State management avec persistance
-   **Vue Router** : Navigation SPA
-   **Axios** : Client HTTP
-   **Tailwind CSS** : Framework CSS utility-first

## 📦 Installation

```bash
# Installer les dépendances
npm install

# Démarrer le serveur de développement
npm run dev

# Build pour la production
npm run build

# Prévisualiser le build
npm run preview
```

## 🔧 Configuration

### Variables d'environnement

Créer un fichier `.env` à la racine du projet :

```env
VITE_API_BASE_URL=http://localhost:8000/api
```

### Backend Laravel

Le backend doit être démarré sur `http://localhost:8000` avec les routes suivantes :

-   `POST /api/auth/login` - Authentification
-   `POST /api/auth/register` - Inscription

**Exemple de réponse attendue :**

```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

## 📁 Structure du projet

```
frontend/
├── src/
│   ├── components/        # Composants réutilisables
│   ├── composables/       # Composables Vue (useUsers, etc.)
│   ├── router/            # Configuration des routes
│   ├── services/          # Services API (axios client)
│   ├── stores/            # Stores Pinia (auth, etc.)
│   ├── views/             # Pages/Vues
│   │   ├── LoginView.vue
│   │   ├── RegisterView.vue
│   │   └── DashboardView.vue
│   ├── App.vue
│   ├── main.js
│   └── style.css
├── .env                   # Variables d'environnement
├── tailwind.config.js     # Config Tailwind
├── vite.config.js         # Config Vite
└── package.json
```

## 🔐 Flux d'authentification

1. **Inscription/Connexion**

    - L'utilisateur remplit le formulaire
    - Envoi des credentials à l'API backend
    - Réception du token + user data
    - Stockage dans le store Pinia (persisté en localStorage)

2. **Navigation**

    - Le router vérifie l'authentification avant chaque route
    - Redirection vers `/login` si non authentifié
    - Redirection vers `/dashboard` si déjà connecté

3. **Requêtes API**

    - Le token est automatiquement attaché à chaque requête
    - Intercepteur Axios gère l'ajout du header Authorization

4. **Déconnexion**
    - Suppression du token et des données utilisateur
    - Redirection vers la page de connexion

## 🎨 Services API

### Service API générique (`src/services/api.js`)

Client Axios configurable avec :

-   Gestion d'erreurs centralisée
-   Intercepteurs de requêtes
-   Timeout configurable (10s)
-   Support FormData automatique

### Service MyApi (`src/services/myApi.js`)

Instance préconfigurée pour le backend Laravel :

```javascript
import { api } from "@/services/myApi";

// Utilisation
const response = await api.post("/auth/login", {
    data: { email, password },
});
```

## 🧩 Composables

### useUsers (`src/composables/useUsers.js`)

Composable pour gérer les opérations CRUD sur les utilisateurs :

```javascript
import { useUsers } from "@/composables/useUsers";

const { users, loading, error, fetchUsers, createUser } = useUsers();

await fetchUsers();
```

## 📱 Routes disponibles

| Route        | Composant     | Protection | Description              |
| ------------ | ------------- | ---------- | ------------------------ |
| `/`          | -             | -          | Redirect vers `/login`   |
| `/login`     | LoginView     | Guest      | Formulaire de connexion  |
| `/register`  | RegisterView  | Guest      | Formulaire d'inscription |
| `/dashboard` | DashboardView | Auth       | Dashboard protégé        |

## 🐛 Dépannage

### Le token n'est pas envoyé avec les requêtes

Vérifier que `authStore.initializeAuth()` est appelé dans `main.js` après la création du store.

### Erreur CORS

Configurer le backend Laravel pour accepter les requêtes depuis `http://localhost:5173`.

### LocalStorage bloqué

Vérifier que le navigateur n'est pas en mode privé et que les cookies/localStorage sont autorisés.

## 📝 Prochaines étapes

-   [ ] Gestion des erreurs réseau (affichage toast)
-   [ ] Refresh token automatique
-   [ ] Gestion de l'expiration du token
-   [ ] Page de profil utilisateur
-   [ ] Implémentation des modules API backend
-   [ ] Tests unitaires (Vitest)
-   [ ] Tests E2E (Cypress/Playwright)
