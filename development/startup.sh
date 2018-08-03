#!/usr/bin/env bash
#
if [ "$ROOT_PATH" == '' ]; then
	export ROOT_PATH
	ROOT_PATH="$(git rev-parse --show-toplevel)"
fi

initial_setup="$1"

if [ "$initial_setup" != '' ] || [ ! -d "${ROOT_PATH}/development/vendor/docker-magento" ]; then
	if [ -d "${ROOT_PATH}/development/vendor/docker-magento" ]; then
		rm -rf "${ROOT_PATH}/development/vendor/docker-magento"
	fi

	mkdir -p "${ROOT_PATH}/development/vendor/docker-magento"
	cd "${ROOT_PATH}/development/vendor/docker-magento" || exit
	git init
	git remote add origin https://github.com/markoshust/docker-magento.git
	git fetch origin
	git checkout 14.0.0 -- compose/magento-2
	mv compose/magento-2/* .
	rm -rf compose .git

	cd "${ROOT_PATH}/development" || exit

	cp "${ROOT_PATH}/development/vendor/docker-magento/bin/download" "${ROOT_PATH}/development/bin/"

	"./bin/download" with-samples-2.2.4

	rm -rf "${ROOT_PATH}/development/vendor/docker-magento/src"

	cd "${ROOT_PATH}/development/vendor/docker-magento" || exit

	ln -s "${ROOT_PATH}/development/src"

	cp -f "${ROOT_PATH}/development/overrides/docker-compose.yml" .

	docker-compose up -d

	# shellcheck disable=1003
	sed '/currency/a\'$'\n''  --backend-frontname=admin \\'$'\n' "bin/setup" > "bin/setup2"

	chmod +x "bin/setup2"

	"./bin/setup2"
else
	cd "${ROOT_PATH}/development/vendor/docker-magento" || exit
	docker-compose up -d
fi

# Enable XDebug extension in docker container
"./bin/xdebug" enable
docker-compose kill
docker-compose up -d

# MacOS only: Enable local alias network for XDebug
# TODO: Make a custom docker image for PHP 7.1 fpm so that this is unnecessary
sudo ifconfig lo0 alias 10.254.254.254 255.255.255.0
