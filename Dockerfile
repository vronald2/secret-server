FROM php:7.2-fpm

RUN apt update  && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev zlib1g-dev libicu-dev g++  libpng-dev  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/   && docker-php-ext-install -j$(nproc) intl pdo_mysql bcmath mbstring exif gd
RUN apt-get update &&  apt-get install -y libpq-dev zlib1g-dev  libzip-dev libpng-dev  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pdo pdo_pgsql pgsql mysqli pdo pdo_mysql zip 

