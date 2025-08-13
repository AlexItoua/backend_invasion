#!/bin/bash

# Run database migrations
php artisan migrate --force

# Start the Apache server in the foreground
apache2-foreground
