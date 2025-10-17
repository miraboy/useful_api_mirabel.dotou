# Guide de test - Frontend Vue.js

## ✅ Checklist de test

### 1. Démarrage de l'application

-   [x] Serveur Vite lancé sur http://localhost:5173
-   [ ] Backend Laravel lancé sur http://localhost:8000
-   [ ] Page de login s'affiche correctement

### 2. Test d'inscription (Register)

1. Naviguer vers http://localhost:5173/ (redirige vers /login)
2. Cliquer sur "S'inscrire"
3. Remplir le formulaire :
    - Nom : `Test User`
    - Email : `test@example.com`
    - Mot de passe : `password123`
    - Confirmation : `password123`
4. Soumettre le formulaire
5. ✅ Doit créer le compte et rediriger vers /dashboard

### 3. Test de connexion (Login)

1. Après inscription, cliquer sur "Déconnexion"
2. Retour sur la page /login
3. Remplir le formulaire :
    - Email : `test@example.com`
    - Mot de passe : `password123`
4. Soumettre le formulaire
5. ✅ Doit connecter et rediriger vers /dashboard

### 4. Test de persistance

1. Connecté sur /dashboard
2. Rafraîchir la page (F5)
3. ✅ Doit rester connecté (token persisté)
4. Ouvrir les DevTools > Application > Local Storage
5. ✅ Doit voir la clé "auth" avec token et user

### 5. Test de protection des routes

1. Déconnecté, essayer d'accéder à http://localhost:5173/dashboard
2. ✅ Doit rediriger vers /login
3. Connecté, essayer d'accéder à http://localhost:5173/login
4. ✅ Doit rediriger vers /dashboard

### 6. Test de déconnexion

1. Connecté sur /dashboard
2. Cliquer sur "Déconnexion"
3. ✅ Doit effacer le token et rediriger vers /login
4. Vérifier Local Storage
5. ✅ La clé "auth" ne doit plus contenir de token

## 🔍 Points de vérification

### Console navigateur

-   Pas d'erreurs JavaScript
-   Requêtes API réussies (200/201)
-   Token attaché aux requêtes protégées

### Network (DevTools)

```
POST http://localhost:8000/api/register → 201
POST http://localhost:8000/api/login → 200
GET http://localhost:8000/api/user → 200 (avec header Authorization)
```

### LocalStorage

```javascript
{
  "auth": {
    "token": "1|xxx...xxx",
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com"
    }
  }
}
```

## 🐛 Résolution des problèmes courants

### Erreur CORS

**Symptôme** : Erreur "CORS policy" dans la console

**Solution** : Vérifier que Laravel accepte les requêtes de http://localhost:5173

```php
// config/cors.php
'allowed_origins' => ['http://localhost:5173'],
```

### Token non envoyé

**Symptôme** : 401 Unauthorized sur /user

**Solution** : Vérifier que `initializeAuth()` est appelé dans main.js

### Erreur 404 sur routes

**Symptôme** : 404 en naviguant directement vers /login

**Solution** : Toujours accéder via la racine http://localhost:5173/ qui gère le routing

### Tailwind ne fonctionne pas

**Symptôme** : Pas de styles, boutons non stylisés

**Solution** :

1. Vérifier que style.css contient les directives @tailwind
2. Redémarrer le serveur Vite
3. Vider le cache du navigateur

## 📊 Commandes utiles

```bash
# Dev server
npm run dev

# Build production
npm run build

# Preview build
npm run preview

# Check errors
npm run lint (si configuré)
```

## 🎯 Prochaines améliorations

-   [ ] Gestion des erreurs avec toast notifications
-   [ ] Loading spinner global
-   [ ] Validation côté client des formulaires
-   [ ] Messages d'erreur détaillés du backend
-   [ ] Page 404 personnalisée
-   [ ] Animations de transition entre pages
