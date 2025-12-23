include .env

ENV_MODE=${ENV}

DOCKER_BEFORE_UP_ARGS=# не применимо в окружении production

ifeq ($(ENV_MODE), development)

DOCKER_BEFORE_UP_ARGS=-f docker-compose.yml -f docker-compose.local.override.yml

endif

ifeq ($(ENV_MODE), testing)

DOCKER_BEFORE_UP_ARGS=-f docker-compose.test.yml

endif

install:
	@$(MAKE) -s down
	@$(MAKE) -s docker-build
	@$(MAKE) -s up
	@$(MAKE) -s migrate

up: docker-up
down: docker-down

restart:
	@$(MAKE) -s down
	@$(MAKE) -s up

ps:
	@docker-compose -p ${DOCKER_PROJECT} ps

logs:
	@docker-compose -p ${DOCKER_PROJECT} logs -f $(target)

docker-up:
	@docker-compose -p ${DOCKER_PROJECT} ${DOCKER_BEFORE_UP_ARGS} up -d
	@$(MAKE) -s bin-refresh-prices

docker-down:
	@docker-compose -p ${DOCKER_PROJECT} down --remove-orphans

docker-build: \
	docker-build-common-tools \
	docker-build-app-php-fpm \
	docker-build-app-nginx \
	docker-build-swagger

docker-build-common-tools:
	@docker build --target=common-tools \
	-t ${DOCKER_REGISTRY}/${DOCKER_COMMON_TOOLS_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} -f ./docker/Dockerfile .

docker-build-app-nginx:
	@docker build --target=nginx \
	-t ${DOCKER_REGISTRY}/${DOCKER_NGINX_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} -f ./docker/Dockerfile .

docker-build-app-php-fpm:
	@docker build --target=fpm \
	--build-arg ENV=${ENV} \
	-t ${DOCKER_REGISTRY}/${DOCKER_PHP_APP_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} -f ./docker/Dockerfile .

docker-build-swagger:
	@docker build --target=swagger \
	-t ${DOCKER_REGISTRY}/${DOCKER_SWAGGER_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} -f ./docker/Dockerfile .

docker-logs:
	@docker-compose -p ${DOCKER_PROJECT} logs -f

wait-db:
	@docker run --network=${DOCKER_PROJECT}_default \
		--rm ${DOCKER_REGISTRY}/${DOCKER_COMMON_TOOLS_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} \
		wait-for ${DB_HOST}:3306 -t 0

app-php-cli-exec:
	@docker-compose -p ${DOCKER_PROJECT} ${DOCKER_BEFORE_UP_ARGS} exec php-fpm $(cmd)

app-php-cli-run:
	@docker-compose -p ${DOCKER_PROJECT} ${DOCKER_BEFORE_UP_ARGS} run --rm php-fpm $(cmd)

run-bin:
	@$(MAKE) app-php-cli-run cmd="./bin"

bin-init-prices:
	@$(MAKE) app-php-cli-exec cmd="./bin prices:init"

bin-clear-prices:
	@$(MAKE) app-php-cli-exec cmd="./bin prices:clear"

bin-refresh-prices:
	@$(MAKE) bin-clear-prices
	@$(MAKE) bin-init-prices

php-app-publish-dev-dependencies:
	@docker run --rm -d --name php-app_dep_extractor ${DOCKER_REGISTRY}/${DOCKER_PHP_APP_IMAGE_NAME}:${DOCKER_IMAGE_VERSION}
	@if [ -d $(PWD)/vendor ]; then rm -r $(PWD)/vendor; fi
	@docker cp php-app_dep_extractor:/var/www/html/vendor $(PWD)/vendor
	@if [ -d $(PWD)/tests/vendor ]; then rm -r $(PWD)/tests/vendor; fi
	@docker cp php-app_dep_extractor:/var/www/html/tests/vendor $(PWD)/tests/vendor
	@docker stop php-app_dep_extractor

migrate:
	@$(MAKE) -s wait-db
	@$(MAKE) app-php-cli-run cmd="./vendor/bin/phinx migrate"

seed:
	@$(MAKE) app-php-cli-run cmd="./vendor/bin/phinx seed:run -v"

run-tests:
	@$(MAKE) restart
	@$(MAKE) wait-db
	@$(MAKE) bin-refresh-prices
	@$(MAKE) migrate
	@$(MAKE) app-php-cli-run cmd="find ./runtime -name '*.json' -type f -delete"
	@$(MAKE) app-php-cli-run cmd="cp -r tests/tests/Support/Data/fixtures/*.json /var/www/html/runtime/"
	@$(MAKE) app-php-cli-run cmd="./tests/vendor/bin/codecept run --steps --debug --config=/var/www/html/tests/codeception.yml"
	@$(MAKE) down

composer-require:
	@$(MAKE) app-php-cli-run cmd="composer require $(pkg)"

spa-install:
	@docker build --target=frontend-builder \
	--build-arg USER=1000 \
	--build-arg GROUP=1000 \
	-t ${DOCKER_REGISTRY}/${DOCKER_FRONTEND_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} -f ./docker/Dockerfile .
	@docker run --rm -v $(PWD)/frontend:/app ${DOCKER_REGISTRY}/${DOCKER_FRONTEND_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} yarn install

spa-build:
	@docker run --rm -v $(PWD)/frontend:/app -v $(PWD)/web:/app-web \
		-e API_AUTH_KEY=${API_AUTH_KEY} \
		-e API_BASE_URL=http://localhost:${APP_WEB_PORT}${API_BASE_URL} \
	${DOCKER_REGISTRY}/${DOCKER_FRONTEND_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} yarn run build

spa-dev-up:
	@$(MAKE) -s up
	@docker run --rm -d -v $(PWD)/frontend:/app -v $(PWD)/web:/app-web \
		--name ${DOCKER_PROJECT}-spa-dev \
		-p ${SPA_DEV_PORT}:5173 \
		-e API_AUTH_KEY=${API_AUTH_KEY} \
		-e API_BASE_URL=http://localhost:${APP_WEB_PORT}${API_BASE_URL} \
		${DOCKER_REGISTRY}/${DOCKER_FRONTEND_IMAGE_NAME}:${DOCKER_IMAGE_VERSION} yarn run dev && \
	echo "\033[0;32mSPA приложение запущено в режиме разработки по адресу http://localhost:${SPA_DEV_PORT}\033[0m\n\033[0;33mПо готовности доработок завершите режим разработки командой make spa-dev-down\033[0m"

spa-dev-down:
	@$(MAKE) -s down
	@docker stop ${DOCKER_PROJECT}-spa-dev && \
	echo "\033[0;32mРежим разработки SPA приложения остановлен.\033[0m\n\033[0;33mДля сборки production-ready SPA приложения выполните команду make spa-build\033[0m"

