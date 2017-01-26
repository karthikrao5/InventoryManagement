#!/bin/bash
sudo cp -rp public/core/ /var/www/html
sudo cp -p public/index.php /var/www/html
sudo cp -p public/.htaccess /var/www/html
sudo cp -rp vendor /var/www

