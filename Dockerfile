FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


FROM php:8.2-cli-bookworm

RUN apt-get update \
    && apt-get install -y \
        git \
        unzip \
        libpq-dev \
        libicu-dev \
        python3 \
        python3-venv \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

COPY --from=frontend /app/public/build /app/public/build

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

RUN python3 -m venv /opt/lstm-venv \
    && /opt/lstm-venv/bin/pip install --no-cache-dir -r requirements-lstm.txt

RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

ENV LSTM_PYTHON_BINARY=/opt/lstm-venv/bin/python

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]