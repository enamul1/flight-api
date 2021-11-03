FROM php:7.3-fpm

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php composer-setup.php \
  && php -r "unlink('composer-setup.php');" \
  && chmod +x composer.phar \
  && mv composer.phar /usr/local/bin/composer


RUN apt-get update && apt-get install -y \
    git build-essential libmcrypt-dev libreadline-dev zip unzip libmagickwand-dev

RUN pecl install imagick \
    && docker-php-ext-enable imagick

RUN apt-get install -y libicu-dev \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl
WORKDIR /var/www


# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY . /var/www
RUN chown -R www:www /var/www
# Change current user to www
USER www


CMD ["/usr/local/sbin/php-fpm"]
