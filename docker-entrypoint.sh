#!/usr/bin/env bash

echo Starting server

set -u
#set -e

# Build parameters file
DB_HOST=${SYMFONY__CLEANSTREET__DATABASE_HOST:-cleanstreet-db.service.consul}
DB_PORT=${SYMFONY__CLEANSTREET__DATABASE_PORT:-5432}
cat > /srv/web/heelenschoon/app/config/parameters.yml <<EOF
parameters:
   database_host: ${DB_HOST}
   database_port: ${DB_PORT}
   database_name: ${SYMFONY__CLEANSTREET__DATABASE_NAME}
   database_user: ${SYMFONY__CLEANSTREET__DATABASE_USER}
   database_password: ${SYMFONY__CLEANSTREET__DATABASE_PASSWORD}
   secret: ${SYMFONY__CLEANSTREET__SECRET}
   app_messagebird_key: ${SYMFONY__CLEANSTREET__APP_MESSAGEBIRD_KEY}
   app_phone_enabled: ${SYMFONY__CLEANSTREET__APP_PHONE_ENABLED}
   piwik_site_id: ${SYMFONY__CLEANSTREET__PIWIK_SITE_ID}
   app_geocoder_baseurl: ${SYMFONY__CLEANSTREET__APP_GEOCODER_BASEURL}
   cookie_secure: true
   trusted_proxies:
        - 127.0.0.1
        - 10.0.0.0/8
        - 172.16.0.0/12
        - 192.168.0.0/16
   swift_auth_url: ${SYMFONY__CLEANSTREET__SWIFT_AUTH_URL}
   swift_region: ${SYMFONY__CLEANSTREET__SWIFT_REGION}
   swift_user_name: ${SYMFONY__CLEANSTREET__SWIFT_USER_NAME}
   swift_user_domain_id: ${SYMFONY__CLEANSTREET__SWIFT_USER_DOMAIN_ID}
   swift_user_password: ${SYMFONY__CLEANSTREET__SWIFT_USER_PASSWORD}
   swift_project_id: ${SYMFONY__CLEANSTREET__SWIFT_PROJECT_ID}
   swift_container_prefix: ${SYMFONY__CLEANSTREET__SWIFT_CONTAINER_PREFIX}
   swift_external_domain: ${SYMFONY__CLEANSTREET__SWIFT_EXTERNAL_DOMAIN}
EOF

# Run composer scripts
php composer.phar install -d heelenschoon/ --no-progress

# Clear and warm prod cache
php heelenschoon/bin/console cache:clear --env=${CLEANSTREET_ENV:-prod}
# Postgres / Postgis
php heelenschoon/bin/console doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\";"
php heelenschoon/bin/console doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS \"postgis\";"
php heelenschoon/bin/console doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS \"postgis_topology\";"
php heelenschoon/bin/console doctrine:migrations:migrate

# Create data/tmp/cache/log/thumbnails dir
[ -d heelenschoon/var/data ] || mkdir -p heelenschoon/var/data
#chown -R www-data:www-data heelenschoon/var heelenschoon/web && find heelenschoon/var heelenschoon/web -type d -exec chmod -R 0770 {}\; && find heelenschoon/var heelenschoon/web -type f -exec chmod -R 0660 {}\;
chown -R www-data:www-data heelenschoon/var

# Start services
service php7.3-fpm start
nginx -g "daemon off;"
