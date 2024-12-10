# Use the official PHP-FPM image as the base image
FROM php:8.1-fpm

# Create a non-root user
RUN groupadd -g 1000 appuser && useradd -r -u 1000 -g appuser appuser

# Set working directory inside the container and grant permissions to the user
WORKDIR /var/www/html/laravel
# ENV COMPOSER_ALLOW_SUPERUSER=1


RUN chown -R appuser:appuser /var/www/html/laravel

RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install zip


# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip

RUN docker-php-ext-install gettext


# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y awscli

# COPY etc/configs/env.stg .env

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    libtiff-dev \
    libwebp-dev \
    libtiff-dev \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libtiff-dev \
    libde265-dev \
    libheif-dev \
    clang \
    libxml2-dev \
    bzip2 \
    pkg-config \
    libtool \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libtiff-dev \
    git \
    wget \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring

RUN docker-php-ext-enable intl mbstring

RUN echo 'export $（strings /proc/1/environ | grep AWS_CONTAINER_CREDENTIALS_RELATIVE_URI）' >> /etc/bashrc

# COPY ./etc/configs/env.dev .env


# USER appuser

# Copy the source code into the container
COPY . .

# RUN composer update


# Expose the port on which the application will run
EXPOSE 8000 9000

ARG NEW_RELIC_LICENSE_KEY
ENV NEW_RELIC_LICENSE_KEY=$NEW_RELIC_LICENSE_KEY

ARG NEW_RELIC_APP_NAME
ENV NEW_RELIC_APP_NAME=$NEW_RELIC_APP_NAME

RUN \
    cd /tmp \
    # Discover the latest released version:
    && export NEW_RELIC_AGENT_VERSION=$(curl -s https://download.newrelic.com/php_agent/release/ | grep -o '[1-9][0-9]\?\(\.[0-9]\+\)\{3\}' | head -n1) \
    # Discover libc provider
    && export NR_INSTALL_PLATFORM=$(ldd --version 2>&1 | grep -q musl && echo "linux-musl" || echo "linux") \
    # Download the discovered version:
    && curl -o newrelic-php-agent.tar.gz https://download.newrelic.com/php_agent/release/newrelic-php5-${NEW_RELIC_AGENT_VERSION}-${NR_INSTALL_PLATFORM}.tar.gz \
    # Install the downloaded agent:
    && tar xzf newrelic-php-agent.tar.gz \
    && NR_INSTALL_USE_CP_NOT_LN=1 NR_INSTALL_SILENT=0 ./*/newrelic-install install \
    # Configure the agent to use license key from NEW_RELIC_LICENSE_KEY env var:
    && sed -ie 's/[ ;]*newrelic.license[[:space:]]=.*/newrelic.license='"$NEW_RELIC_LICENSE_KEY"'/' $(php-config --ini-dir)/newrelic.ini \
    # Configure the agent to use app name from NEW_RELIC_APP_NAME env var:
    && sed -ie 's/[ ;]*newrelic.appname[[:space:]]=.*/newrelic.appname='"$NEW_RELIC_APP_NAME"'/' $(php-config --ini-dir)/newrelic.ini \
    # Cleanup temporary files:
    && rm newrelic-php-agent.tar.gz && rm -rf newrelic-php5-*-linux

# Command to run the application
CMD ["./etc/scripts/entrypoint.sh"]
