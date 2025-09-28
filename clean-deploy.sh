#!/bin/bash

echo "🧹 Cleaning Laravel caches..."

# Nettoyer tous les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

echo "📦 Cleaning Composer cache..."
composer clear-cache
composer dump-autoload --optimize

echo "🗑️ Cleaning bootstrap cache files..."
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/routes-*.php
rm -f bootstrap/cache/events.php

echo "📂 Checking storage permissions..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

echo "✅ All caches cleared!"

echo "🚀 Committing changes and triggering Railway deploy..."
git add .
git commit -m "Clean cache and force Railway redeploy"
git push origin main

echo "🎯 Deploy triggered! Check Railway dashboard."
