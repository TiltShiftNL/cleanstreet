FROM nginx:latest
MAINTAINER datapunt@amsterdam.nl

EXPOSE 80

# add Sury.org PHP 7.1 packages
RUN apt install apt-transport-https ca-certificates wget \
 && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
 && sh -c 'echo "deb https://packages.sury.org/php/ stretch main" > /etc/apt/sources.list.d/php.list' \
 && apt update
# install php packages
RUN apt-get update && apt-get install -y git vim wget cron rsync php7.1-fpm php7.1-intl php7.1-pgsql php7.1-curl php7.1-cli php7.1-gd php7.1-mbstring php7.1-mcrypt php7.1-opcache php7.1-sqlite3 php7.1-xml php7.1-xsl php7.1-zip php7.1-json php7.1-xmlrpc iputils-ping \
 && apt-get -y upgrade && apt-get -y dist-upgrade && apt-get check && apt-get clean

# create basic directory
RUN mkdir -p /srv/web/heelenschoon

# project setup
COPY . /srv/web/heelenschoon
WORKDIR /srv/web
RUN wget https://getcomposer.org/composer.phar

# nginx and php setup
COPY Docker/cleanstreet.vhost /etc/nginx/conf.d/heelenschoon.vhost.conf
RUN rm /etc/nginx/conf.d/default.conf \
 && sed -i '/\;listen\.mode\ \=\ 0660/c\listen\.mode=0666' /etc/php/7.1/fpm/pool.d/www.conf \
 && sed -i '/pm.max_children = 5/c\pm.max_children = 20' /etc/php/7.1/fpm/pool.d/www.conf \
 && sed -i '/\;pm\.max_requests\ \=\ 500/c\pm\.max_requests\ \=\ 100' /etc/php/7.1/fpm/pool.d/www.conf \
 && echo "server_tokens off;" > /etc/nginx/conf.d/extra.conf \
 && echo "client_max_body_size 20m;" >> /etc/nginx/conf.d/extra.conf \
 && sed -i '/upload_max_filesize \= 2M/c\upload_max_filesize \= 20M' /etc/php/7.1/fpm/php.ini \
 && sed -i '/post_max_size \= 8M/c\post_max_size \= 21M' /etc/php/7.1/fpm/php.ini \
 && sed -i '/\;date\.timezone \=/c\date.timezone = Europe\/Amsterdam' /etc/php/7.1/fpm/php.ini \
 && sed -i '/\;security\.limit_extensions \= \.php \.php3 \.php4 \.php5 \.php7/c\security\.limit_extensions \= \.php' /etc/php/7.1/fpm/pool.d/www.conf \
 && sed -e 's/;clear_env = no/clear_env = no/' -i /etc/php/7.1/fpm/pool.d/www.conf

# only install dependencies
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN php composer.phar install -d heelenschoon/ --prefer-dist --no-progress --no-suggest --no-scripts

# run
COPY docker-entrypoint.sh /docker-entrypoint.sh
# Permission denied error while launching container
RUN chmod +x /docker-entrypoint.sh
CMD /docker-entrypoint.sh
