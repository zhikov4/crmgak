#!/bin/bash
set -e

export PORT=${PORT:-8000}

cat > /etc/nginx/sites-available/default << EOF
server {
    listen ${PORT} default_server;
    root /var/www/public;
    index index.php;
    location / { try_files \$uri \$uri/ /index.php?\$query_string; }
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php-fpm -D
exec nginx -g "daemon off;"