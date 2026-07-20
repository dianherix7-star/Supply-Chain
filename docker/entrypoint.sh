#!/bin/sh
set -e

echo "🚀 Starting Supply Chain Global on Railway..."

# Railway sets PORT env variable automatically
PORT="${PORT:-8080}"

echo "📡 Using port: $PORT"

# ─── Generate nginx config dynamically with correct PORT ───
cat > /etc/nginx/nginx.conf <<EOF
worker_processes auto;
error_log /dev/stderr warn;
pid /var/run/nginx.pid;

events {
    worker_connections 1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;
    sendfile on;
    keepalive_timeout 65;
    client_max_body_size 50M;

    access_log /dev/stdout;

    server {
        listen ${PORT};
        server_name _;
        root /var/www/html/public;
        index index.php index.html;

        charset utf-8;

        location / {
            try_files \$uri \$uri/ /index.php?\$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
            include fastcgi_params;
            fastcgi_read_timeout 300;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
}
EOF

echo "✅ Nginx config generated for port $PORT"

# ─── Generate APP_KEY jika belum ada ───────────────────────
if [ -z "$APP_KEY" ]; then
    echo "⚙️  Generating APP_KEY..."
    php artisan key:generate --force
fi

# ─── Storage link ─────────────────────────────────────────
echo "🔗 Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

# ─── Clear cache dulu sebelum cache ulang ─────────────────
echo "🧹 Clearing old cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# ─── Cache untuk production ────────────────────────────────
echo "⚡ Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─── Run migrations ────────────────────────────────────────
echo "🗄️  Running migrations..."
php artisan migrate --force

# ─── Set permissions ───────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chown -R www-data:www-data /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# ─── Create supervisor log dir ─────────────────────────────
mkdir -p /var/log/supervisor

echo "✅ All setup done! Starting Nginx + PHP-FPM..."

# ─── Start Nginx + PHP-FPM via Supervisor ──────────────────
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
