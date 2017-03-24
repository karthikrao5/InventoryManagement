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

Or alternatively, use Vagrant to setup development virtual machine automatically and use it.
=======
API routes:

Two creation routes:
1. Equipment creation POST /v1/equipments with body as:
{
  "equipment_type": "laptop",
  "department_tag": "Some department specific serial number here",
  "gt_tag": null,
  "status": "loaned",
  "loaned_to": "krao34",
  "comment": "laptop has a bad battery",
  "attributes": {
  	"cpu": "Intel Q6600",
  	"gpu": "NVIDIA GTX 1080",
  	"RAM": "16",
  	"screen_size": "15"
  },
  "logs": {
  	"action_by": "Justin Filosetta"
  }
}

2. EquipmentType creation POST /v1/equipmenttypes with body as
{
	"name": "laptop",
	"attributes": 
	[
		{
			"name":"something",
			"required": 0,
			"unique": 1,
			"data_type": "string",
			"regex": "adfsasdf",
			"help_comment": "something" 
		},
		{
			"name":"something else",
			"required": 1,
			"unique": 1,
			"data_type": "boolean",
			"regex": "some regex",
			"help_comment": "HELP ME"
		}
	]
}

Note: EquipmentType must be created first since an Equipment maps to a single EquipmentType