FROM php:8.2-cli

RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 8080

WORKDIR /var/www/html

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html/public", "/var/www/html/public/router.php"]
