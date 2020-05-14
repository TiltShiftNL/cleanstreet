FROM nginx:1.17
MAINTAINER apps@tiltshift.nl

EXPOSE 80

ARG DEBIAN_FRONTEND=noninteractive

# add Sury.org PHP 7.1 packages
RUN apt-get update \
 && apt install -yq apt-transport-https ca-certificates wget \
 && apt update
# install php packages
RUN apt-get update && apt-get install -yq git vim wget cron rsync \
 php-fpm \
 php-intl \
 php-pgsql \
 php-curl \
 php-cli \
 php-gd \
 php-mbstring \
 # php-mcrypt \ DEPRECATED
 php-opcache \
 php-sqlite3 \
 php-xml \
 php-xsl \
 php-zip \
 php-json \
 php-xmlrpc \
 iputils-ping \
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
 && sed -i '/\;listen\.mode\ \=\ 0660/c\listen\.mode=0666' /etc/php/7.3/fpm/pool.d/www.conf \
 && sed -i '/pm.max_children = 5/c\pm.max_children = 20' /etc/php/7.3/fpm/pool.d/www.conf \
 && sed -i '/\;pm\.max_requests\ \=\ 500/c\pm\.max_requests\ \=\ 100' /etc/php/7.3/fpm/pool.d/www.conf \
 && echo "server_tokens off;" > /etc/nginx/conf.d/extra.conf \
 && echo "client_max_body_size 20m;" >> /etc/nginx/conf.d/extra.conf \
 && sed -i '/upload_max_filesize \= 2M/c\upload_max_filesize \= 20M' /etc/php/7.3/fpm/php.ini \
 && sed -i '/post_max_size \= 8M/c\post_max_size \= 21M' /etc/php/7.3/fpm/php.ini \
 && sed -i '/\;date\.timezone \=/c\date.timezone = Europe\/Amsterdam' /etc/php/7.3/fpm/php.ini \
 && sed -i '/\;security\.limit_extensions \= \.php \.php3 \.php4 \.php5 \.php7/c\security\.limit_extensions \= \.php' /etc/php/7.3/fpm/pool.d/www.conf \
 && sed -e 's/;clear_env = no/clear_env = no/' -i /etc/php/7.3/fpm/pool.d/www.conf

# only install dependencies
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN php composer.phar install -d heelenschoon/ --prefer-dist --no-progress --no-scripts

# run
COPY docker-entrypoint.sh /docker-entrypoint.sh
# Permission denied error while launching container
RUN chmod +x /docker-entrypoint.sh
CMD /docker-entrypoint.sh
