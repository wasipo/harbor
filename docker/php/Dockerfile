FROM php:8.4-fpm-alpine

# 環境変数
ENV TZ=UTC \
    LANG=en_US.UTF-8 \
    LANGUAGE=en_US:en \
    LC_ALL=en_US.UTF-8 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer

# 必要なパッケージのインストール (Alpine用)
RUN apk add --no-cache \
    git \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    freetype-dev \
    unzip \
    vim

# ロケール設定 (Alpine版はmusl-localesで対応)

# PHP拡張のインストール
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        zip \
        intl \
        mbstring \
        gd \
        bcmath

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP設定
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini
COPY ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# opcache.iniは未作成のためコメントアウト
# COPY ./docker/php/conf/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# 作業ディレクトリ
WORKDIR /var/www/html

# 権限設定 (Alpine版)
# www-dataユーザーは既に存在するため、UIDのみ変更
RUN sed -i 's/^www-data:x:82:82:/www-data:x:1000:1000:/' /etc/passwd && \
    sed -i 's/^www-data:x:82:/www-data:x:1000:/' /etc/group

EXPOSE 9000

CMD ["php-fpm"]