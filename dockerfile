FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev libzip-dev libjpeg-dev libfreetype6-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring xml gd zip bcmath \
    && apt-get clean

RUN docker-php-ext-install pdo_pgsql
# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Laravel app files
COPY . .


# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R ug+rwx storage bootstrap/cache

# Apache config
RUN echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Copy and make the entrypoint executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]




# # # Utiliser PHP avec Apache
# FROM php:8.2-apache

# # Installer les dépendances système
# RUN apt-get update && apt-get install -y \
#     zip \
#     unzip \
#     git \
#     curl \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     libzip-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     libpq-dev \
#     bison \
#     re2c \
#     && docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring xml gd zip bcmath \
#     && apt-get clean

# # Configurer Apache pour Laravel
# RUN echo "<VirtualHost *:80>\n\
#     DocumentRoot /var/www/html/public\n\
#     <Directory /var/www/html/public>\n\
#         AllowOverride All\n\
#         Require all granted\n\
#     </Directory>\n\
# </VirtualHost>" > /etc/apache2/sites-available/000-default.conf \
#     && a2enmod rewrite

# # Ajouter une configuration spécifique pour le répertoire public/storage
# RUN echo "<Directory /var/www/html/public/storage>\n\
#         AllowOverride all\n\
#         Require all granted\n\
#         Options Indexes FollowSymLinks\n\
#     </Directory>" >> /etc/apache2/apache2.conf

# # Ajouter un ServerName pour éviter les avertissements Apache
# RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# RUN echo "<Directory /var/www/html/storage>\n\
#         AllowOverride all\n\
#         Require all granted\n\
#         Options Indexes FollowSymLinks\n\
#     </Directory>" >> /etc/apache2/apache2.conf

# # Ajouter un ServerName pour éviter les avertissements Apache
# RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# # Installer Composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# # Configurer le répertoire de travail
# WORKDIR /var/www/html

# # Copier le projet Laravel
# COPY . .

# # Installer les dépendances Laravel
# RUN composer install --no-dev --optimize-autoloader

# # Corriger les permissions pour Laravel
# # RUN chown -R www-data:www-data storage public/storage bootstrap/cache && \
# #     chmod -R 777 storage public/storage bootstrap/cache
# RUN chmod -R 777 storage public/storage bootstrap/cache && \
#     chown -R www-data:www-data storage public/storage bootstrap/cache
    
# # Créer manuellement le lien symbolique
# RUN ln -s /var/www/html/storage/app/public /var/www/html/public/storage || true
    
# # Exposer le port 80
# EXPOSE 80

# # Script d'entrée pour exécuter les migrations avant de démarrer Apache
# COPY docker-entrypoint.sh /usr/local/bin/

# RUN chmod +x /usr/local/bin/docker-entrypoint.sh


# ENTRYPOINT ["docker-entrypoint.sh"]

# CMD ["apache2-foreground"]



# # Utiliser PHP avec FPM
# # FROM php:8.2-fpm

# # # Installer les dépendances système
# # RUN apt-get update && apt-get install -y \
# #     zip \
# #     unzip \
# #     git \
# #     curl \
# #     libpng-dev \
# #     libonig-dev \
# #     libxml2-dev \
# #     libzip-dev \
# #     libjpeg-dev \
# #     libfreetype6-dev \
# #     libpq-dev \
# #     nginx \
# #     && docker-php-ext-configure gd --with-freetype --with-jpeg \
# #     && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring xml gd zip bcmath \
# #     && apt-get clean

# # # Configurer Nginx
# # COPY nginx.conf /etc/nginx/nginx.conf
# # COPY default.conf /etc/nginx/conf.d/default.conf

# # # Installer Composer
# # RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# # # Configurer le répertoire de travail
# # WORKDIR /var/www/html

# # # Copier le projet Laravel
# # COPY . .

# # # Installer les dépendances Laravel
# # RUN composer install --no-dev --optimize-autoloader

# # # Corriger les permissions pour Laravel
# # RUN chmod -R 775 storage bootstrap/cache && \
# #     chown -R www-data:www-data storage bootstrap/cache

# # # Créer le lien symbolique pour les fichiers publics de stockage
# # RUN php artisan storage:link

# # # Exposer le port 80
# # EXPOSE 80

# # # Script d'entrée pour exécuter les migrations et démarrer Nginx + PHP-FPM
# # COPY docker-entrypoint.sh /usr/local/bin/
# # RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# # ENTRYPOINT ["docker-entrypoint.sh"]

# # CMD ["php-fpm"]