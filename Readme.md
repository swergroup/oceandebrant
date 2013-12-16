# OceanDebrant

VirtualBox + Digital Ocean Vagrant project starter kit.

This Vagrant setup create a local development setup alongside a Digital Ocean droplet with the same setup.

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

```
git clone https://github.com/swergroup/oceandebrant.git
cd oceandebrant
vagrant up --provider virtualbox
vagrant up --provider digital_ocean
```

### Configuration

#### Vagrant 

* Copy `Vagrantfile-sample` to `Vagrantile`
* Modify `ocean.client_id`, `ocean.api_key` and `ocean.region` with your own Digital Ocean settings.

#### WordPress

Fork the repository, create a branch and modify WordPress configuration files:

* `config/nginx/sites-available/wordpress.conf`
* `www/wordpress/composer.json`
* `www/wordpress/wp-cli.yml`

