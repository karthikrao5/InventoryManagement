#!/bin/bash
sudo rm -rf /var/www/html/*
sudo rm -rf /var/www/src
sudo rm -rf /var/www/vendor

sudo cp -a public/. /var/www/html
sudo cp -a vendor /var/www
sudo cp -a src /var/www

sudo chmod -R 777 /var/www/src/log/
