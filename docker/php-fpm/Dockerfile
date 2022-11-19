FROM ubuntu:latest
WORKDIR "/application"

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php8.1-common php8.1-fpm  php8.1-zip php8.1-memcached  php8.1-intl php8.1-bcmath php8.1-mysql php8.1-ldap php8.1-soap \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*


# Install git
RUN apt-get update \
    && apt-get -y install git \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*


#COPY composer.sh ./
#RUN chmod +x composer.sh
#RUN ./composer.sh



