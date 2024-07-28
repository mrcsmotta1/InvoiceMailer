FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip

  # Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd sockets

COPY 90-xdebug.ini "${PHP_INI_DIR}/conf.d"
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# Install redis
RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /app

RUN chown -R www-data:www-data /app/storage

EXPOSE 9000

