#!/bin/bash

echo "🚀 Starting Laravel Invasion Backend..."

# Variables d'environnement
export PORT=${PORT:-8080}
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}

echo "📋 Environment: $APP_ENV"
echo "🔧 Debug mode: $APP_DEBUG"
echo "🌐 Port: $PORT"

# Vérifier les permissions
echo "🔐 Checking file permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Attendre la base de données
echo "💾 Waiting for database connection..."
sleep 3

# Générer la clé d'application si nécessaire
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force --no-interaction
fi

# Vérifier la connexion à la base de données
echo "🔍 Testing database connection..."
php artisan migrate:status --no-interaction || {
    echo "❌ Database connection failed. Continuing anyway..."
}

# Exécuter les migrations
echo "📊 Running database migrations..."
php artisan migrate --force --no-interaction || {
    echo "⚠️ Migrations failed, but continuing..."
}

# Seed si nécessaire (uniquement en développement)
if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force --no-interaction || {
        echo "⚠️ Seeding failed, but continuing..."
    }
fi

# Créer le lien de stockage
echo "🔗 Creating storage link..."
php artisan storage:link --force || {
    echo "⚠️ Storage link creation failed, but continuing..."
}

# Optimisations pour la production
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing for production..."

    # Cache des configurations
    php artisan config:cache --no-interaction || echo "⚠️ Config cache failed"

    # Cache des routes
    php artisan route:cache --no-interaction || echo "⚠️ Route cache failed"

    # Cache des vues
    php artisan view:cache --no-interaction || echo "⚠️ View cache failed"

    # Cache des événements
    php artisan event:cache --no-interaction || echo "⚠️ Event cache failed"
else
    echo "🧹 Clearing caches for development..."
    php artisan config:clear --no-interaction
    php artisan route:clear --no-interaction
    php artisan view:clear --no-interaction
    php artisan cache:clear --no-interaction
fi

# Vérifier l'état de l'application
echo "🏥 Application health check..."
php artisan about --no-interaction || echo "⚠️ Health check not available"

# Message de démarrage
echo ""
echo "🎉 Laravel application ready!"
echo "📡 API Base URL: /api/v1"
echo "🔒 Authentication: Laravel Sanctum"
echo "📄 Documentation: /api-info"
echo "🏥 Health Check: /health"
echo ""

# Démarrer le serveur
echo "🌟 Starting Laravel server on 0.0.0.0:$PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT --env=$APP_ENV
