#!/bin/bash

echo "🚀 Starting Laravel Invasion API on Railway..."

# Variables Railway
export PORT=${PORT:-8080}
export APP_ENV=production
export APP_DEBUG=false

echo "🌐 Port: $PORT"
echo "🔧 Environment: $APP_ENV"
echo "🐛 Debug: $APP_DEBUG"

# Permissions essentielles
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app storage/framework storage/logs

# Test de connexion à la base de données
echo "💾 Testing database connection..."
php artisan migrate:status || {
    echo "❌ Database connection issue, checking config..."
    echo "DB_HOST: $DB_HOST"
    echo "DB_PORT: $DB_PORT"
    echo "DB_DATABASE: $DB_DATABASE"
}

# Générer la clé si nécessaire
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force --no-interaction
fi

# Migrations avec retry
echo "📊 Running migrations..."
for i in {1..3}; do
    php artisan migrate --force --no-interaction && break
    echo "Migration attempt $i failed, retrying in 5 seconds..."
    sleep 5
done

# Optimisations production
echo "⚡ Optimizing for production..."
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

# Storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force --no-interaction

# Vérification finale
echo "🏥 Final health check..."
php artisan about --no-interaction

echo ""
echo "✅ Laravel ready!"
echo "🌍 URL: $APP_URL"
echo "📡 API: $APP_URL/api/v1"
echo "🏥 Health: $APP_URL/health"
echo ""

# Démarrage du serveur
echo "🎯 Starting server on 0.0.0.0:$PORT"
exec php artisan serve --host=0.0.0.0 --port=$PORT --env=production
