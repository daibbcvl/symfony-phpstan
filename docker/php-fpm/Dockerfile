FROM magento/magento-cloud-docker-php:8.1-fpm-1.3.1

WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install zip unzip php8.1-common php8.1-hash php8.1-openssl php8.1-simplexml php8.1-xsl php8.1-zip php8.1-curl php8.1-gd  php8.1-intl php8.1-bcmath php8.1-sockets  php8.1-mysql php8.1-ldap php8.1-soap php8.1-pdo_mysql php8.1-sodium \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*


RUN pecl install -f libsodium
RUN echo "extension = sodium" > /usr/local/etc/php/conf.d/sodium.ini

# Add xdebug
RUN pecl install xdebug-3.1.5
RUN docker-php-ext-enable xdebug


# Configure Xdebug
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9001" >> /usr/local/etc/php/conf.d/xdebug.ini


# Install git
RUN apt-get update \
    && apt-get -y install git vim \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
