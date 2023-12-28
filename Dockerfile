FROM php:8.2-fpm

ARG WWW_USER=1000
ARG WWW_GROUP=100

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get -y update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    cron

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip curl intl

# Clean cache
RUN apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Create user
RUN groupadd --force -g $WWW_GROUP webapp
RUN useradd -ms /bin/bash --no-user-group -g $WWW_GROUP -u $WWW_USER webapp

# cron config https://stackoverflow.com/questions/43323754/cannot-make-remove-an-entry-for-the-specified-session-cron
RUN rm -rf /etc/cron.*/* \
    && sed -i '/session    required     pam_loginuid.so/c\#session    required   pam_loginuid.so' /etc/pam.d/cron \
    && echo '* * * * * su webapp -c "/usr/local/bin/php /var/www/artisan schedule:run" > /proc/1/fd/1 2>/proc/1/fd/2' > /etc/cron.d/cron \
    && chmod 0644 /etc/cron.d/cron \
    && crontab /etc/cron.d/cron
