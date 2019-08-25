FROM chialab/php-dev:7.2-apache

LABEL maintainer="contact@apprendre.co"

ENV APACHE_DOCUMENT_ROOT /var/www/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN apt-get update && apt-get install -y locales

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo fr_FR.UTF-8 UTF-8 > /etc/locale.gen && locale-gen

WORKDIR /var/www