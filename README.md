# InventoryManagement
GT Junior Design Part 2 Spring 2017

Karthik Rao, Will Christian, Joe Sadler, Andres Littig, Byung Kang
----
Getting started with this project

1. Install CentOS 6.8 on VirtualBox.

2. Clone this repo on the Desktop directory or anywhere you like.

3. Run `./setup.sh` in scripts/centos/ directory. Make sure that the script has execute permission `chmod 777 setup.sh`.

4. Reboot the virtual machine.

5. Install VirtualBox Guest Additions for better development environment.

6. Run `./move.sh' in the repo to copy files to /var/www/html

To upgrade git version to 2.5.3 (required to push to repo)

```
sudo yum remove -y git
cd /usr/src
sudo wget https://www.kernel.org/pub/software/scm/git-2.5.3.tar.gz
sudo tar xzf git-2.5.3.tar.gz
cd git-2.5.3
sudo make prefix=/usr/local/git all
sudo make prefix=/usr/local/git install
sudo echo "export PATH=$PATH:/usr/local/git/bin" >> /etc/bashrc
sudo source /etc/bashrc
sudo -s # to switch to root (required)
source /etc/bashrc
```
<<<<<<< HEAD

Or alternatively, use Vagrant to setup development virtual machine automatically and use it.
=======
API routes:

1. GET all inventory items "/inventory/add"
2. POST item to add to db "/inventory/add" with JSON body of what you want to add
3. GET query item by ObjectID "/inventory?id=someIntObjectId"
ex "http://localhost/inventory?id=5890001e1502bdf19873571a"

testing slack integration. disregard this line.
>>>>>>> d66862c5e82a992643ae4db61caf0b51e96d6e31
