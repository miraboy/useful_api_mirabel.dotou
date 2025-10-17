# Configuration CORS pour Laravel

## 🔧 Si vous rencontrez des erreurs CORS

### Solution 1 : Ajouter le package Laravel CORS

```bash
composer require fruitcake/laravel-cors
```

### Solution 2 : Configuration manuelle dans bootstrap/app.php

Ajouter dans `bootstrap/app.php` :

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        \Illuminate\Http\Middleware\HandleCors::class,
    ]);
})
```

### Solution 3 : Headers manuels dans le Controller

Ajouter dans `app/Http/Controllers/AuthController.php` :

```php
return response()->json($data)
    ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
```

### Solution 4 : Middleware personnalisé

Créer `app/Http/Middleware/Cors.php` :

```php
<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }
}
```

Puis ajouter dans `bootstrap/app.php` :

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\App\Http\Middleware\Cors::class);
})
```

## ✅ Test CORS

Ouvrir la console du navigateur sur http://localhost:5173 et exécuter :

```javascript
fetch("http://localhost:8000/api/login", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email: "test@test.com", password: "test" }),
})
    .then((r) => r.json())
    .then(console.log)
    .catch(console.error);
```

Si ça fonctionne → CORS OK ✅  
Si erreur "CORS policy" → Appliquer une des solutions ci-dessus ❌
