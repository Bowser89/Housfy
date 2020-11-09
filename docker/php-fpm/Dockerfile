FROM php:fpm-alpine

# Install dependencies
RUN apk --update --no-cache add \
	zip \
	vim \
	unzip \
	curl \
	libmemcached-dev

RUN docker-php-ext-install pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

ARG USER_ID
ARG GROUP_ID

RUN addgroup -g $GROUP_ID user
RUN	adduser --disabled-password -u $USER_ID -G user user
RUN chown -R user:user /var/www

USER user

CMD composer install; php-fpm

EXPOSE 9000
