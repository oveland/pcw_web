FROM ubuntu:20.04
LABEL maintainer="Oveland ™ <oscarivelan@gmail.com>"

ARG php_version
ARG ng_root
ARG time_zone

ENV TZ=${time_zone}
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install tools, PHP and Nginx
RUN apt-get update
RUN apt-get install -y software-properties-common
RUN apt-get update \
	&& apt-get install -y libmcrypt-dev \
		libxml2-dev \
		libicu-dev \
		libzip-dev \
		wget \
		nano \
		htop \
		git \
		npm \
        curl \
        lsof \
        nginx \
		unzip \
        netcat \
		locate \
        net-tools \
        gettext-base \
	&& apt-get update \
	&& apt-get install -y zip \
	    postgresql-client
RUN apt-get install -y ca-certificates apt-transport-https software-properties-common;

RUN apt-get update  \
    && apt-get install -y language-pack-en-base  \
    && export LC_ALL=en_US.UTF-8  \
    && export LANG=en_US.UTF-8 \
    && LC_ALL=en_US.UTF-8 add-apt-repository -y ppa:ondrej/php

RUN apt-get update
RUN apt-get install -y php${php_version} \
        php${php_version}-cli \
		php${php_version}-fpm \
		php${php_version}-curl \
		php${php_version}-gd \
		php${php_version}-pgsql \
		php${php_version}-mbstring \
		php${php_version}-common \
		php${php_version}-fileinfo \
		php${php_version}-intl \
		php${php_version}-zip \
		php${php_version}-xml \
		php${php_version}-xsl \
		php${php_version}-gd;

# Copy base config PHP
COPY config/php.ini /etc/php/${php_version}/fpm/php.ini.template
COPY config/php.ini /etc/php/${php_version}/cli/php.ini.template

# Copy base config Nginx
COPY config/nginx.conf /etc/nginx/nginx.conf
COPY config/server.conf /etc/nginx/conf.d/server.template
RUN mkdir /etc/nginx/cache
RUN mkdir /var/www/${ng_root}

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', '/tmp/composer-setup.php');" \
    && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /tmp/composer-setup.php

# Install node
#RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
#RUN apt install -y nodejs

# Copy script for general congis like auto run php and nginx server
COPY config/configure.sh /tmp/
RUN ["chmod", "+x", "/tmp/configure.sh"]

COPY config/.bashrc ~/
RUN bash

RUN apt -y install jpeginfo
RUN apt -y install cron

COPY config/crontab /etc/crontabs/root

RUN apt-get update \
	&& apt-get install -y php${php_version}-interbase
RUN apt -y install alien
RUN #docker-php-ext-install interbase
RUN #docker-php-ext-install pdo_firebird

WORKDIR /var/www/${ng_root}
