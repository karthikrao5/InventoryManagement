#!/bin/bash
sudo cp -a public/. /var/www/html
sudo cp -a vendor /var/www
sudo cp -a src /var/www

sudo chmod -R 777 /var/www/src/Helper/odmcache/
sudo chmod -R 777 /var/www/src/log/