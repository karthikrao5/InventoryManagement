#!/bin/bash

# install apache2
sudo apt install apache2

# install php
sudo apt install php libapache2-mod-php php-mcrypt

# install composer (for slim framework)
sudo apt install composer

# install slim framework
cd ..
composer require slim/slim "^3.0"
