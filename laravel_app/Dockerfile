FROM php:8.2-apache

ARG UID=1000
ARG USER=dev

ENV UID=$UID

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -G root,www-data -u 1000 -d /home/$USER $USER
RUN mkdir -p /home/$USER/.composer
RUN chown -R $USER:$USER /home/$USER

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    curl \
    libzip-dev \
    unzip \
    zip \
    libcurl4-openssl-dev \
    libssl-dev \
    wget \
    default-mysql-client
    # mysql-server \
    # php-cli \
    # && docker-php-ext-install pdo_mysql

RUN apt-get update && apt-get install -y libxml2-dev \
    && docker-php-ext-install dom pdo_mysql

# RUN docker-php-ext-install gettext intl mysqli gd

# RUN echo "memory_limit = 1024M" >> /usr/local/etc/php.ini

RUN chown -R $USER:$USER /var/www/html
# RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo 'DirectoryIndex index.php' >> /etc/apache2/sites-available/000-default.conf


WORKDIR /var/www/html

# Install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader


CMD bash -c 'touch .env ; php artisan key:generate ;  php artisan migrate --force ; php artisan db:seed --class=AdminUserSeeder ; php artisan optimize:clear ; php artisan storage:link ; chmod a+rwx storage/ -R ; apache2-foreground'
