#!/bin/bash

# This script is to provision development and production servers.
# It is being used by Vagrant during provisioning stage of a VM.

# Add MongoDB repository to yum by copying mongodb-org-3.4.repo to /etc/yum.repos.d/
sudo cp mongodb/mongodb-org-3.4.repo /etc/yum.repos.d

# Update system.
sudo yum -y update

# Install development packages.
sudo yum -y install curl-devel expat-devel gettext-devel openssl-devel zlib-devel gcc perl-ExtUtils-MakeMaker

# Install apache2.
sudo yum -y install httpd

# Start apache2.
sudo service httpd start

# Install MongoDB.
sudo yum -y install mongodb-org

# Start MongoDB.
sudo service mongod start

# Install php 5.3.
sudo yum -y install php

# Upgrade php 5.3 to 5.6.
sudo wget https://dl.fedoraproject.org/pub/epel/epel-release-latest-6.noarch.rpm
sudo rpm -Uvh epel-release-latest-6.noarch.rpm
sudo wget http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
sudo rpm -Uvh remi-release-6*.rpm
sudo rm epel-release-latest-6.noarch.rpm remi-release-6*.rpm
sudo cp php/remi.repo /etc/yum.repos.d/
sudo yum -y upgrade php*

# Install Composer (php package manager).
sudo curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/bin/composer
sudo chmod +x /usr/bin/composer

# Install MongoDB driver for php.
sudo yum -y install php-pecl-mongo

# Replace welcome.conf in apache2 config folder.
sudo cp apache2/welcome.conf /etc/httpd/conf.d/

# Allow http connections to iptables.
sudo iptables -I INPUT 4 -p tcp -m state --state NEW -m tcp --dport 80 -j ACCEPT
sudo /etc/init.d/iptables save

# Enable mod rewrite for apache2.
sudo cp apache2/httpd.conf /etc/httpd/conf/
sudo service httpd restart

# Enable httpd auto start at boot.
sudo chkconfig --add httpd
sudo chkconfig httpd on

# Enable apache2 to make network connection.
sudo /usr/sbin/setsebool -P httpd_can_network_connect 1

# Upgrade git to 2.11.1
sudo yum -y remove git
cd /usr/src
sudo wget https://www.kernel.org/pub/software/scm/git/git-2.11.1.tar.gz
sudo tar xzf git-2.11.1.tar.gz
cd git-2.11.1
sudo make prefix=/usr/local/git all
sudo make prefix=/usr/local/git install
sudo echo 'export PATH=$PATH:/usr/local/git/bin' >> /etc/bashrc
sudo ln -s /usr/local/git/bin/git /usr/bin/git
sudo source /etc/bashrc

# Fill the database
cd /tmp/scripts/centos/mongodb/
/bin/bash fillDB.sh

# Clone git repository in home directory
cd ~/
git clone https://github.com/karthikrao5/InventoryManagement.git

# Install Slim framework by using composer.lock file in repo.
cd InventoryManagement/
composer install