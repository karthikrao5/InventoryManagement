#!/bin/bash

# update packages info
sudo apt update

# upgrade installed packages
sudo apt -y upgrade

# install apache2
sudo apt -y install apache2

# install php
sudo apt -y install php7.0 libapache2-mod-php7.0

# enable rewrite module
sudo a2enmod rewrite

# change apache config to allow override to all by replacing config file
sudo cp apache2.conf /etc/apache2/

# install composer (for slim framework)
sudo apt -y install composer

# install slim framework
cd ..
#composer require slim/slim "^3.0"  # This is for initial install. No need to run this more than once since repo contains composer.lock
composer install
cd Scripts

# install mongoDB
# https://www.digitalocean.com/community/tutorials/how-to-install-mongodb-on-ubuntu-16-04
sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv EA312927  # import key
echo "deb http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.2 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.2.list  # create a list file for MongoDB
sudo apt update
sudo apt install -y mongodb-org

sudo cp mongodb.service /etc/systemd/system/mongodb.service  # add mongoDB to systemd

sudo systemctl start mongodb
sudo systemctl enable mongodb
#sudo systemctl status mongodb

# to remove remove startup warnings (optional)
# https://docs.mongodb.com/manual/tutorial/transparent-huge-pages/#transparent-huge-pages-thp-settings
# reboot system to apply changes
sudo cp disable-transparent-hugepages /etc/init.d/disable-transparent-hugepages
sudo chmod 755 /etc/init.d/disable-transparent-hugepages
sudo update-rc.d disable-transparent-hugepages defaults

# install php-mongodb driver
sudo apt -y install php-mongodb
