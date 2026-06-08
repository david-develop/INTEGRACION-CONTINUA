FROM php:8.1-apache
RUN apt-get update && apt-get install -y --no-install-recommends curl \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html/subidas
EXPOSE 80
