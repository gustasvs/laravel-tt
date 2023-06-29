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
    default-mysql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY app /var/www/html/app
COPY database /var/www/html/database
COPY public /var/www/html/public

RUN apt-get update && apt-get install -y libxml2-dev \
    && docker-php-ext-install dom pdo_mysql

RUN chown -R $USER:$USER /var/www/html
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN echo 'DirectoryIndex index.php' >> /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY . /var/www/html

# Install dependencies
COPY composer.json composer.lock ./
RUN composer install 
#--ignore-platform-reqs --no-scripts --no-autoloader

RUN composer dump-autoload --optimize --classmap-authoritative

CMD bash -c 'touch .env ; sleep 10;  php artisan key:generate ;  php artisan migrate --force ; php artisan db:seed --class=AdminUserSeeder ; php artisan optimize:clear ; php artisan storage:link ; chmod a+rwx storage/ -R ; apache2-foreground'