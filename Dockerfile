FROM php:8.2-apache

RUN docker-php-ext-install mysqli

WORKDIR /app
COPY . /app

RUN rm -rf /var/www/html && ln -s /app /var/www/html

RUN mkdir -p /app/uploads/messages && chown -R www-data:www-data /app/uploads

EXPOSE 80
CMD ["apache2-foreground"]