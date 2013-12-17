# OceanDebrant

VirtualBox + [DigitalOcean](https://www.digitalocean.com/?refcode=f0f450f173bf) Vagrant project starter kit.

This Vagrant setup create a local development setup alongside a Digital Ocean droplet with the same setup.

## Features

## Enhanced LEMP stack

* Debian Wheezy
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
cp vagrant-config.yml.sample vagrant-config.yml
vi vagrant-config.yml
vagrant up --provider [virtualbox|digital_ocean]
```

### Configuration

Personal settings via `vagrant-config.yml` file:

```
hostname: box hostname
  
virtualbox:
    private_ip: virtualbox private networking IP address
  
digitalocean:
    client_id: [DigitalOcean](https://www.digitalocean.com/?refcode=f0f450f173bf) client ID
    api_key: DO API key
    image: DO droplet image, default: "Debian 7.0 x64"
    region: DO region
    private_key: Path to SSH private key
```

#### WordPress

Fork the repository, create a branch and modify WordPress configuration files:

* `config/nginx/sites-available/(local|remote)-wordpress.conf`
* `www/wordpress/composer.json`
* `www/wordpress/wp-cli.yml`

