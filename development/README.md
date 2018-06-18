# Magento 2 Module Development 
## Purpose
The purpose of this section is to spin up a Magento 2 development instance
## Requirements
- [Docker](http://get.docker.com)
- [Docker-compose](https://docs.docker.com/compose/)
## Setup
- `cd development`
- `./startup.sh`
- Modify your `/etc/hosts` file so that `magento2.test` points to your local machine
## Shutdown
- `cd development`
- `./shutdown.sh`
## Usage
- The magento instance can be found on `magento2.test`
- The admin URI is `magento2.test/admin`
   - Username: `magento2`
   - Password: `magento2`
## Notes
Passing any parameter to `startup.sh` will cause it to delete the existing infrastructure and redownload
