FROM php:5.6-apache

RUN apt-get update && apt-get install -y libmcrypt-dev \
    && pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli

RUN echo 'zend_extension="/usr/local/lib/php/extensions/no-debug-non-zts-20151012/xdebug.so"' >> /usr/local/etc/php/php.ini \
    && echo 'xdebug.remote_port=9000' >> /usr/local/etc/php/php.ini \
    && echo 'xdebug.remote_enable=1' >> /usr/local/etc/php/php.ini \
    && echo 'xdebug.remote_connect_back=1' >> /usr/local/etc/php/php.ini \
    && echo 'date.timezone=Europe/Madrid' >> /usr/local/etc/php/php.ini

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
