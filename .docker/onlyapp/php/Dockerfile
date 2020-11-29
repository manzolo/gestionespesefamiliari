ARG MYSQL_HOST
ARG MYSQL_PORT

FROM php:7.4.10-fpm as build

RUN apt-get update \
    && apt-get install -y --no-install-recommends vim curl debconf subversion git apt-transport-https apt-utils \
    build-essential locales acl mailutils wget nodejs zip unzip libzip-dev \
    gnupg gnupg1 gnupg2 \
    zlib1g-dev \
    libpng-dev \
    sudo

RUN docker-php-ext-install pdo pdo_mysql zip gd

COPY .docker/onlyapp/php/php.ini /usr/local/etc/php/php.ini
COPY .docker/onlyapp/php/php-fpm-pool.conf 	/usr/local/etc/php/pool.d/www.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
	mv composer.phar /usr/local/bin/composer

RUN wget --no-check-certificate https://phar.phpunit.de/phpunit-6.5.3.phar && \
    mv phpunit*.phar phpunit.phar && \
    chmod +x phpunit.phar && \
    mv phpunit.phar /usr/local/bin/phpunit

RUN	echo "deb https://deb.nodesource.com/node_6.x jessie main" >> /etc/apt/sources.list.d/nodejs.list && \
	wget -nv -O -  https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - && \
	echo "deb-src https://deb.nodesource.com/node_6.x jessie main" >> /etc/apt/sources.list.d/nodejs.list && \
    apt-get update && \
	apt-get install -y --force-yes nodejs && \
	rm -f /etc/apt/sources.list.d/nodejs.list

RUN groupadd dev -g 999
RUN useradd dev -g dev -d /home/dev -m
RUN passwd -d dev

RUN rm -rf /var/lib/apt/lists/*
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "it_IT.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

RUN echo "dev ALL=(ALL) ALL" > /etc/sudoers

WORKDIR /home/wwwroot/gestionespesefamiliari

COPY . .

ENV SYMFONY_ENV=prod

RUN rm -rf .git && \
    rm -rf app/cache && \
    rm -rf app/logs && \
    rm -rf ./app/config/parameters.yml && \
    cp .docker/onlyapp/php/init.sh /usr/local/bin/init.sh && \
    cp ./app/config/parameters.yml.dist ./app/config/parameters.yml && \
    composer install --no-dev --optimize-autoloader

FROM ubuntu:20.04

RUN apt-get update -y && DEBIAN_FRONTEND=noninteractive apt-get install -y libmcrypt-dev libonig-dev zlib1g-dev \
apache2 php7.4 php7.4-mysql libapache2-mod-php7.4 php-mbstring php-intl php-pdo php-mysql php-pdo-mysql php-gd php-zip php-xml curl \
libpng-dev libzip-dev acl

WORKDIR /var/www/html

COPY --from=build /home/wwwroot/gestionespesefamiliari /var/www/html
RUN chown -R www-data:www-data /var/www/html

COPY --from=build /home/wwwroot/gestionespesefamiliari/.docker/onlyapp/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY --from=build /home/wwwroot/gestionespesefamiliari/.docker/onlyapp/apache/start-apache /usr/local/bin/

ENV MYSQL_HOST=mysqlhost
ENV MYSQL_PORT=3306

RUN chmod +x /usr/local/bin/start-apache

RUN rm /var/www/html/index.html

RUN apachectl configtest
RUN a2enmod php7.4
RUN a2enmod rewrite
RUN a2enmod env

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

EXPOSE 80

CMD ["/usr/local/bin/start-apache"]
