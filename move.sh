#!/bin/bash
<<<<<<< HEAD
sudo cp -a public/. /var/www/html
sudo cp -a vendor /var/www
=======
#sudo cp -rp public/core/ /var/www/html
#sudo cp -p public/index.php /var/www/html
#sudo cp -p public/.htaccess /var/www/html
sudo cp -rp public/* /var/www/html
sudo cp -rp src/ /var/www/html
sudo cp -rp vendor /var/www
>>>>>>> d66862c5e82a992643ae4db61caf0b51e96d6e31

