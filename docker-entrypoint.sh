#!/usr/bin/env bash

echo Starting server

set -u
#set -e

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