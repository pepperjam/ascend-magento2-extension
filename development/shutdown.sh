#!/usr/bin/env bash
if [ "$ROOT_PATH" == '' ]; then
	export ROOT_PATH
	ROOT_PATH="$(git rev-parse --show-toplevel)"
fi

cd "${ROOT_PATH}/development/vendor/docker-magento" || exit

docker-compose down
