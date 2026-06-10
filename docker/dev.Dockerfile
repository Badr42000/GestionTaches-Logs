FROM php:8.2-cli

RUN apt-get update && apt-get install -y unzip git && \
    docker-php-ext-install pdo pdo_mysql sockets && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
