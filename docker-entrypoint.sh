#!/usr/bin/env bash

echo Starting server

set -u
#set -e

DB_HOST=${SYMFONY__CLEANSTREET__DATABASE_HOST:-cleanstreet-db.service.consul}
DB_PORT=${SYMFONY__CLEANSTREET__DATABASE_PORT:-5432}

cat > /srv/web/heelenschoon/app/config/parameters.yml <<EOF
parameters:
   database_host: ${DB_HOST}
   database_port: ${DB_PORT}
   database_name: ${SYMFONY__CLEANSTREET__DATABASE_NAME}
   database_user: ${SYMFONY__CLEANSTREET__DATABASE_USER}
   database_password: ${SYMFONY__CLEANSTREET__DATABASE_PASSWORD}
   mailer_transport: ${SYMFONY__CLEANSTREET__MAILER_TRANSPORT}
   mailer_host: ${SYMFONY__CLEANSTREET__MAILER_HOST}
   mailer_user: ${SYMFONY__CLEANSTREET__MAILER_USER}
   mailer_password: ${SYMFONY__CLEANSTREET__MAILER_PASSWORD}
   mailer_port: ${SYMFONY__CLEANSTREET__MAILER_PORT}
   mailer_encryption: ${SYMFONY__CLEANSTREET__MAILER_ENCRYPTION}
   secret: ${SYMFONY__CLEANSTREET__SECRET}
   messagebird_accountkey: ${SYMFONY__CLEANSTREET__APP_MESSAGEBIRD_KEY}
   messagebird_enable: ${SYMFONY__CLEANSTREET__APP_PHONE_ENABLED}
   piwik_site_id: ${SYMFONY__CLEANSTREET__PIWIK_SITE_ID}
   trusted_proxies:
        - 127.0.0.1
        - 10.0.0.0/8
        - 172.16.0.0/12
        - 192.168.0.0/16
EOF

# Already in Dockerfile (?)
php composer.phar install -d heelenschoon/

php heelenschoon/bin/console cache:clear --no-warmup --env=prod

# Postgres / Postgis
php heelenschoon/bin/console doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\";"
php heelenschoon/bin/console doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS \"postgis\";" 
php heelenschoon/bin/console doctrine:query:sql "CREATE EXTENSION IF NOT EXISTS \"postgis_topology\";"
php heelenschoon/bin/console doctrine:migrations:migrate

[ -d heelenschoon/var/data ] || mkdir -p heelenschoon/var/data && [ -d heelenschoon/web/media ] || mkdir -p heelenschoon/web/media
#chown -R www-data:www-data heelenschoon/var heelenschoon/web && find heelenschoon/var heelenschoon/web -type d -exec chmod -R 0770 {}\; && find heelenschoon/var heelenschoon/web -type f -exec chmod -R 0660 {}\;
chown -R www-data:www-data heelenschoon/var heelenschoon/web

service php7.0-fpm start
nginx -g "daemon off;"