#!/bin/bash

# update
sudo yum -y update

# upgrade git to 2.5.3
sudo yum install -y curl-devel expat-devel gettext-devel openssl-devel zlib-devel
sudo yum install -y gcc perl-ExtUtils-MakeMaker
sudo yum remove -y git
cd /usr/src
sudo wget https://www.kernel.org/pub/software/scm/git/git-2.5.3.tar.gz
sudo tar xzf git-2.5.3.tar.gz
cd git-2.5.3
sudo make prefix=/usr/local/git all
sudo make prefix=/usr/local/git install
# sudo echo "export PATH=$PATH:/usr/local/git/bin" >> /etc/bashrc
# command above must run by root (not sudo)
# run sudo -s to switch to root and run the command
# sudo source /etc/bashrc
# source /etc/bashrc
# run above two commands after echoing the export path to /etc/bashrc

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

# replace welcome.conf
sudo cp apache2/welcome.conf /etc/httpd/conf.d/

# allow http connections to iptables
sudo iptables -I INPUT 4 -p tcp -m state --state NEW -m tcp --dport 80 -j ACCEPT
sudo /etc/init.d/iptables save

# enable mod rewrite
sudo cp apache2/httpd.conf /etc/httpd/conf/
sudo service httpd restart

# enable httpd auto start at boot
sudo chkconfig --add httpd
sudo chkconfig httpd on
