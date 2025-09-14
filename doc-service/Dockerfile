FROM php:8.2-apache

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY src/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
