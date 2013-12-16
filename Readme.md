# OceanDebrant

VirtualBox + Digital Ocean Vagrant project starter kit

## Features

## Enhanced LEMP stack

* Nginx
* PHP5-FPM
* APC
* Memcache
* Percona Server (MySQL)
* Varnish

### WordPress

* Composer
* wp-cli
* Themes: TwentyFourteen, Roots
* Plugins: Pods, UploadPlus, WordPress Importer, W3 Total Cache 

## Setup

* Download and install [wheezy32 Vagrant box](http://tools.swergroup.com/downloads/wheezy32.box):

```
vagrant box add wheezy32 http://tools.swergroup.com/downloads/wheezy32.box
vagrant box add digital_ocean https://github.com/smdahlen/vagrant-digitalocean/raw/master/box/digital_ocean.box
```