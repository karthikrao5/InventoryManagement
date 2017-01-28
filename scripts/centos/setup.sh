#!/bin/bash

# update
sudo yum -y update

# install apache2
sudo yum install httpd

# start apache2 
sudo service httpd start

# add mongoDB repo to yum
sudo cp mongoDB/mongodb-org-3.4.repo /etc/yum.repos.d/
sudo yum -y update

# install mongoDB
sudo yum install -y mongodb-org

# start mongoDB
sudo service mongod start

# install php 5.3
sudo yum install php

# upgrade php 5.3 to 5.6
sudo wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
sudo rpm -Uvh epel-release-latest-6.noarch.rpm
sudo wget http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
sudo rpm -Uvh remi-release-6*.rpm
sudo rm epel-release-latest-6.noarch.rpm remi-release-6*.rpm
sudo cp php/remi.repo /etc/yum.repos.d/
sudo yum -y upgrade php*

# install composer
sudo curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# install slim framework
# uses composer.lock file
cd ../..
composer install
cd scripts/centos

# install mongoDB driver for php
sudo yum -y install php-pecl-mongo
