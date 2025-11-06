FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    nano \
    jq \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    mariadb-client \
    procps \
    e2fsprogs \
    sudo

# Cleans up the package lists after installation, which reduces the final image size.
RUN rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install the PHP extensions Drupal needs
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) gd opcache zip pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

COPY ./docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini

COPY ./docker/xdebug-toggle.sh /usr/local/bin/xdebug
RUN chmod +x /usr/local/bin/xdebug

RUN xdebug off

WORKDIR /var/www/html

RUN composer create-project drupal/recommended-project:11.x-dev .

RUN composer require drush/drush

# Add the local vendor bin to the PATH. This makes 'drush' available everywhere.
ENV PATH="/var/www/html/vendor/bin:${PATH}"

# Point Apache's document root to the correct 'web' directory
RUN sed -i 's!/var/www/html!/var/www/html/web!g' /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN mkdir -p "/var/www/html/custom/module/youtube-embed-formatter"
COPY ./css /var/www/html/custom/module/youtube-embed-formatter/css
COPY ./src /var/www/html/custom/module/youtube-embed-formatter/src
COPY ./templates /var/www/html/custom/module/youtube-embed-formatter/templates
COPY ./composer.json /var/www/html/custom/module/youtube-embed-formatter/composer.json
COPY ./youtube_embed_formatter.info.yml /var/www/html/custom/module/youtube-embed-formatter/youtube_embed_formatter.info.yml
COPY ./youtube_embed_formatter.libraries.yml /var/www/html/custom/module/youtube-embed-formatter/youtube_embed_formatter.libraries.yml
COPY ./docker/CreateDemoArticleCommands.php /var/www/html/custom/module/youtube-embed-formatter/src/Drush/Commands/CreateDemoArticleCommands.php
COPY ./docker/AddYoutubeVideoFieldToArticleCommands.php /var/www/html/custom/module/youtube-embed-formatter/src/Drush/Commands/AddYoutubeVideoFieldToArticleCommands.php

RUN sudo chown -R 1000:1000 /var/www/html

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

COPY ./docker/apache.conf /etc/apache2/conf-available/servername.conf
RUN a2enconf servername

CMD ["apache2-foreground"]
