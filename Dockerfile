FROM php:8.2-apache
RUN apt-get update && apt-get install -y \ docker-php-ext-install mysqli pdo pdo_mysql

COPY apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite