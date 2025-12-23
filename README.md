Калькулятор расчета стоимости доставки
============================
Индекс проекта: CALCANTCS

## Окружение

- WSL / Linux
- docker 1.10.0+
- docker-compose 1.6.0+
- Make

### Переменные окружения

```dotenv
# Режим окружения production/development/testing
ENV=production

# Имя проекта для docker compose
DOCKER_PROJECT=legacy-calculator
# Хранилище контейнеров
DOCKER_REGISTRY=localhost
# Версия контейнеров
DOCKER_IMAGE_VERSION=latest
# Режим отладки
DEBUG=1

# Имя образа контейнера PHP-приложения
DOCKER_PHP_APP_IMAGE_NAME=legacy-calculator-php-app
# Имя образа контейнера веб-сервера
DOCKER_NGINX_IMAGE_NAME=legacy-calculator-nginx
# Имя образа контейнера SwaggerUI
DOCKER_SWAGGER_IMAGE_NAME=legacy-calculator-swagger
# Имя образа контейнера вспомогательных инструментов
DOCKER_COMMON_TOOLS_IMAGE_NAME=legacy-calculator-common-tools
# Имя образа контейнера сборщика клиенсткой части приложения
DOCKER_FRONTEND_IMAGE_NAME=legacy-calculator-frontend

# Порт веб-сервера
APP_WEB_PORT=8089
# Порт режима разработки SPA приложения
SPA_DEV_PORT=5173

# Режим UI приложения SSR/SPA
RENDER_MODE=SSR

# URL API для FRONTEND-части
API_BASE_URL=/api/v2
# Токен авторизации для API
API_AUTH_KEY=

# Имя сервиса обработки вызова PHP приложения
PHP_UPSTREAM_CONTAINER=php-fpm
# Порт сервиса обработки вызова PHP приложения
PHP_UPSTREAM_PORT=9000

# Хост БД
DB_HOST=mariadb
# Имя БД
DB_NAME=calculator
# Пароль для суперпользователя БД
DB_ROOT_PASSWORD=secret
# Порт БД для внешниз подключений
DB_PORT=33061
# Имя пользователя БД приложения
DB_USER=calculator
# Пароль пользователя БД приложения
DB_PASSWORD=secret
```

## Установка

### Установка для продуктивного исполнения

1. Скопировать **.env.dist** в **.env** и актуализировать все параметры
1. Задать переменной окружения режим продуктивного исполнения `ENV=production`
1. Выполнить `make install`
1. Выполнить `make seed`

### (Дополнительно) Для запуска приложения в режиме SPA

1. Установить переменную окужения режима UI приложения `RENDER_MODE=SPA`
1. Выполнить `make spa-install`
1. Выполнить `make spa-build`
1. Выполнить `make install`

### Установка для исполнения в режиме разработки

1. Скопировать **.env.dist** в **.env** и актуализировать все параметры
1. Задать переменной окружения режим продуктивного исполнения `ENV=development`
1. Выполнить `make php-app-publish-dev-dependencies`
1. Выполнить `make install`
1. Выполнить `make seed`

### Установка для исполнения в режиме тестирования

1. Скопировать **.env.dist** в **.env** и актуализировать все параметры
1. Задать переменной окружения режим продуктивного исполнения `ENV=testing`
1. Выполнить `make php-app-publish-dev-dependencies`
1. Выполнить `make install`
1. Выполнить `make run-tests`

## Дополнительно

### Служебное

- `make install` - установка проекта
- `make seed` - заполнение таблицы первоначальными данными
- `make php-app-publish-dev-dependencies` - публикация зависимостей php-приложения для режима разработки
- `make up` - запуск контейнеров
- `make down` - остановка контейнеров
- `make ps` - информация о контейнерах
- `make docker-logs` - просмотр логов контейнеров
- `make run-bin` - запуск консольной реализации
- `make bin-init-prices` - инициализация данных по прайсам в формате json
- `make bin-clear-prices` - очистка данных по прайсам
- `make bin-refresh-prices` - пересоздать прайсы
- `make migrate` - применение миграций
- `make seed` - заполнить БД начальными данными
- `make run-tests` - выполнить автоматизированные тесты
- `make spa-install` - установка зависимостей SPA приложения
- `make spa-build` - выполнить сборку SPA приложения
- `make spa-dev-up` - запустить режим разработки SPA приложения
- `make spa-dev-down` - остановить режим разработки SPA приложения