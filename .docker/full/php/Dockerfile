FROM php:7.4.10-fpm as build

RUN apt-get update \
    && apt-get install -y --no-install-recommends vim curl debconf subversion git apt-transport-https apt-utils \
    build-essential locales acl mailutils wget nodejs zip unzip libzip-dev \
    gnupg gnupg1 gnupg2 \
    zlib1g-dev \
    libpng-dev \
    sudo

RUN docker-php-ext-install pdo pdo_mysql zip gd

COPY .docker/full/php/php.ini /usr/local/etc/php/php.ini
COPY .docker/full/php/php-fpm-pool.conf 	/usr/local/etc/php/pool.d/www.conf

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

RUN rm -rf .git && \
    rm -rf var && \
    rm -rf ./app/config/parameters.yml && \
    cp .docker/full/php/init.sh /usr/local/bin/init.sh && \
    cp ./app/config/parameters.yml.dist ./app/config/parameters.yml && \
    mkdir var && \ 
    composer install

FROM php:7.4-cli-alpine

RUN apk update && apk add libmcrypt-dev netcat-openbsd oniguruma-dev zlib-dev libpng-dev libzip-dev bash
#RUN apt-get update -y && apt-get install -y libmcrypt-dev libonig-dev zlib1g-dev libpng-dev libzip-dev netcat
RUN docker-php-ext-install pdo pdo_mysql mbstring gd zip

WORKDIR /app
COPY --from=build /home/wwwroot/gestionespesefamiliari /app
COPY --from=build /home/wwwroot/gestionespesefamiliari/.docker/full/php/init.sh /usr/local/bin/init.sh

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN chmod +x /usr/local/bin/init.sh

EXPOSE 8030
CMD ["/usr/local/bin/init.sh"]
