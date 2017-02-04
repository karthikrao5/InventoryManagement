Getting started with Vagrant

1. Install latest Vagrant from the link below. Make sure that VirtualBox is installed before installing Vagrant.

	https://www.vagrantup.com/downloads.html
	
2. Clone the git repository to host (not in virtual machine). 
   It is very important to checkout git repository as-is, meaning EOL should be in Unix type (LF).
   If you see some weird errors during the provisioning step, probably your repo is checked out in DOS type EOL (CRLF).
   Vagrant copies "scripts/centos/setup.sh" into the VM and try to read the file. Since the VM (Linux) cannot read
   the file correctly, Vagrant provisioning will spit out weird erros and fail.
   Take a look at this link to avoid/fix the issue.
   
	http://stackoverflow.com/questions/2517190/how-do-i-force-git-to-use-lf-instead-of-crlf-under-windows

3. Download Vagrant box "centos-6.8-x86_64.box" from the team Google Drive.
   It's under /JD Group 154/Vagrant Box/
	
4. Open terminal or cmd and change directory to where "centos-6.8-x86_64.box" is located.

5. Run "vagrant box add centos-6.8-x86_64 centos-6.8-x86_64.box"

6. Change directory to "InventoryManagement/vagrant/". You should have "Vagrantfile" (without extension).

7. Run "vagrant up". You will see some warnings as VM is being created and provisioned. 
   It's part of the normal process so don't worry about it.
   The whole process takes about 15 minutes (in i7-3630QM).
   
8. Once it's ready, you can run "vagrant ssh" to ssh into the VM via terminal or cmd.
   Password for "root" and "vagrant" is "vagrant".
   You can access VM's web server via 127.0.0.1:8080 (host port 8080 is mapped to vm port 80).
   To view port mapping, run "vagrant port".
   
   Look into this document if you would like to use separate SSH client.
   
		https://github.com/Varying-Vagrant-Vagrants/VVV/wiki/Connect-to-Your-Vagrant-Virtual-Machine-with-PuTTY
		
   If you don't know how to setup SSH on your own, it's not worth your time using separate SSH client.
   
9. Easy file sharing.

   By default, "InventoryManagement/vagrant/" (on host) is mapped to "/vagrant" (on VM).
   For our project, "InventoryManagement/vagrant/vm/" (on host) is mapped to "/home/vagrant/InventoryManagement" (on VM).
   
   You can modify files on the host and change will be reflected right away.
   If you are on Windows, EOL conversion from DOS to Unix is recommended. (Google if you don't know!)
   
10. When you are done with VM, you can either run "vagrant suspend" to save current state and stop or
    run "vagrant halt" to gracefully shutdown vm. You can also run "vagrant destroy" to completely remove
	the VM. Take a look at the document below for detail.
	
		https://www.vagrantup.com/docs/getting-started/teardown.html
		
11. For the future updates, please let Byung know if you are installing package that is required for the project.

