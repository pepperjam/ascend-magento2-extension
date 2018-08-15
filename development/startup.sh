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

	echo "Downloading Docker Magento repository..."
	mkdir -p "${ROOT_PATH}/development/vendor/docker-magento"
	cd "${ROOT_PATH}/development/vendor/docker-magento" || exit
	git init
	git remote add origin https://github.com/markoshust/docker-magento.git
	if ! git fetch origin; then
		echo "Git fetch failed."
		exit 10
	fi
	if ! git checkout 14.0.0 -- compose/magento-2; then
		echo "Checkout of proper version failed."
		exit 10
	fi
	mv compose/magento-2/* .
	rm -rf compose .git

	echo "Docker-Magento downloaded..."
	cd "${ROOT_PATH}/development" || exit

	cp "${ROOT_PATH}/development/vendor/docker-magento/bin/download" "${ROOT_PATH}/development/bin/"

	echo "Downloading Magento..."
	"./bin/download" with-samples-2.2.4

	echo "Magento downloaded."

	rm -rf "${ROOT_PATH}/development/vendor/docker-magento/src"

	cd "${ROOT_PATH}/development/vendor/docker-magento" || exit

	ln -s "${ROOT_PATH}/development/src"

	if ! cp -f "${ROOT_PATH}/development/overrides/docker-compose.yml" .; then
		echo "Failed to override docker-compose.yml"
		exit 10
	fi
	if ! cp -f "${ROOT_PATH}/development/overrides/db.env" ./env/; then
		echo "FAIL"
		exit 10
	fi
	echo "Spinning up Docker containers..."
	docker-compose up -d
	echo "Containers running."
	# shellcheck disable=1003
	sed '/currency/a\'$'\n''  --backend-frontname=admin \\'$'\n' "bin/setup" > "bin/setup2"

	echo "Beginning Magento setup..."
	chmod +x "bin/setup2"

	"./bin/setup2"
	echo "Magento setup complete."
else
	cd "${ROOT_PATH}/development/vendor/docker-magento" || exit
	echo "Spinning up Docker containers..."
	docker-compose up -d
	echo "Containers running."
fi

# Enable XDebug extension in docker container
echo "Enabling XDebug..."
"./bin/xdebug" enable
docker-compose kill phpfpm
docker-compose up -d phpfpm
echo "XDebug enabled."

# MacOS only: Enable local alias network for XDebug
# TODO: Make a custom docker image for PHP 7.1 fpm so that this is unnecessary
sudo ifconfig lo0 alias 10.254.254.254 255.255.255.0
