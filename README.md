# InventoryManagement
GT Junior Design Part 2 Spring 2017

Karthik Rao, Will Christian, Joe Sadler, Andres Littig, Byung Kang
----
# Release Notes
**New features for this release:**

**Bug fixes:**

**Known bugs and defects for Front-end:**

**Known bugs and defects for Back-end:**
* Backend access routes are fragile due to unfinished input validation.
* API key feature is not implemented in this release.
* Log information is missing username (action_by) and how the route is accessed (action_via).
----
# Installation Guide
**Getting started with this project:**

1. Install CentOS 6.8 on the server.

2. Clone this repository.

3. Run './setup.sh' in scripts/centos/ directory. Make sure that the script has execute permission `chmod 777 setup.sh`.

4. Run `./move.sh' in the repository

5. Visit http://localhost/ (if accessing from the server) or http://server_url/ to use InventoryManagement system.

Or alternatively, use Vagrant to setup development virtual machine automatically and use it.

**Getting started with Vagrant:**

1. Download Vagrant box 'centos-6.8-x86_64.box' from provided private Google Drive link (will be emailed to you).

2. Install Vagrant. https://www.vagrantup.com/docs/installation/

3. Open terminal and change directory to where 'centos-6.8-x86_64.box' is located.

4. Run 'vagrant box add centos-6.8-x86_64 centos-6.8-x86_64.box' from terminal to add the custom Vagrant box.

5. Clone this repository to host (not in Vagrant virtual machine) and move into 'vagrant' directory. You should have 'Vagrantfile' file (without extension).

6. Run 'vagrant up' to create new virtual machine. You may see warning as virtual machine is being created and provisioned. Depending on your network speed and computer horsepower, this step can take up to 15 minutes.

7. Once 'vagrant up' is done, you can run 'vagrant ssh' to ssh into the virtual machine. It shouldn't ask you for username and password. If for some reason, vagrant ssh or any sudo commands asks you for password, use 'vagrant' as password for both 'root' and 'vagrant'.

8. This repository is automatically cloned into the virtual machine in '/home/vagrant/shared/InventoryManagement'.

9. You should be able to connect to 'http://localhost:8080' to use InventoryManagement system.

Notes: Vagrant maps host port 8080 to virtual machine port 80. Make sure host port 8080 is not in use.
