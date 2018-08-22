# Magento 2 Module Development 
## Purpose
The purpose of this section is to spin up a Magento 2 development instance
## Requirements
- [Docker](http://get.docker.com)
- [Docker-compose](https://docs.docker.com/compose/)
## Setup
- `cd development`
- `./startup.sh`
- Add `127.0.0.1 magento2.test` to your `/etc/hosts` file

Then, open `PHPStorm > Preferences > Languages & Frameworks > PHP` and configure:

- `CLI Interpreter`:
	- Choose `Docker`, then select the `markoshust/magento-php:7-1-fpm` image name.
	- Under `Additional > Debugger Extension`, enter
		- `/usr/local/lib/php/extensions/no-debug-non-zts-20160303/xdebug.so`

- `Docker container`:
	- Ensure a volume binding has been setup for Container path of `/var/www/html` mapped to the Host path of `development/src`.

Open `PHPStorm > Preferences > Languages & Frameworks > PHP > Debug` and set Debug Port to `9001`.

Open `PHPStorm > Preferences > Languages & Frameworks > PHP > DBGp Proxy` and set:

- IDE key: `PHPSTORM`
- Host: `127.0.0.1`
- Port: `9001`

Open `PHPStorm > Preferences > Languages & Frameworks > PHP > Servers` and create a new server:

- Set Name and Host to `magento2.test`
- Set Port to `8000`
- Check the Path Mappings box and map `development/src` to the absolute path of `/var/www/html`

Create a new `PHP Remote Debug` configuration at `Run > Edit Configurations`. Set the Name to `magento2.test`. Check `Filter debug connection by IDE Key`, select the `magento2.test` server, and set IDE key to `PHPSTORM`.

To validate the settings, open up `src/pub/index.php` and set a breakpoint near the beginning of the file. Go to `Run > Debug 'magento2.test'`, and open up `http://magento2.test?XDEBUG_SESSION_START=PHPSTORM`.  You may have to click a link to trigger the first catch  

## Shutdown
- `cd development`
- `./shutdown.sh`
## Usage
- The magento instance can be found on `magento2.test`
- The admin URI is `magento2.test/admin`
   - Username: `magento2`
   - Password: `magento2`

## Notes
- Passing any parameter to `startup.sh` will cause it to delete the existing infrastructure and redownload the Magento source.
- Passing any parameter to `shutdown.sh` will cause it to fully purge the downloaded Magento source.
- There is a branch of this repository called `production-mode` that sets the container's Magento instance into Production mode for testing.
