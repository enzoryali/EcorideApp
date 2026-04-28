FROM php:8.2-apache

# Installation des extensions PHP nécessaires pour Symfony et MySQL
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install intl pdo pdo_mysql zip

# On active le mode Rewrite d'Apache pour gérer les routes Symfony
RUN a2enmod rewrite

# On définit le répertoire de travail
WORKDIR /var/www/html

# On configure Apache pour pointer vers le dossier /public (indispensable pour Symfony)
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# On donne les droits au serveur web sur les dossiers de Symfony
RUN chown -R www-data:www-data /var/www/html