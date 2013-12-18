# OceanDebrant

VirtualBox + [DigitalOcean](https://www.digitalocean.com/?refcode=f0f450f173bf) Vagrant project starter kit.

This Vagrant setup create a local development setup alongside a Digital Ocean droplet with the same setup.

## Setup

### 1. Vagrant Plugins

Install these Vagrant plugins with `vagrant plugin install <plugin-name>`:

* [vagrant-cachier](https://github.com/fgrehm/vagrant-cachier) -- local packages cache
* [vagrant-digitalocean](https://github.com/smdahlen/vagrant-digitalocean) -- digitalocean provider
* [vagrant-vbguest](https://github.com/dotless-de/vagrant-vbguest) -- keep VirtualBox guest additions updated

### 2. Download and configure OceanDebrant

```
git clone https://github.com/swergroup/oceandebrant.git
cd oceandebrant
cp vagrant-config.yml.sample vagrant-config.yml
vi vagrant-config.yml
```

#### Configuration Options

Private settings and options are in the `vagrant-config.yml` file:

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

#### WordPress Configuration

Fork the repository, create a branch and modify WordPress configuration files:

* `config/nginx/sites-available/(local|remote)-wordpress.conf`
* `www/wordpress/composer.json`
* `www/wordpress/wp-cli.yml`

### 3. Provision Scripts

Provision is mantained by three different bash scripts, each one is pluggable with a custom one called `<level>-custom-<script>.sh`, ie: `10-custom-system.sh`. Each script receive the current provider as the first argument, see source code for details.

#### 10-system.sh

* APT sources setup
* APT packages list
* APT install/update logic

#### 20-serverstack.sh

* Percona Server (MySQL)
* PHP5
* Nginx
* Varnish
* Composer
* wp-cli
* system cleaning and service reboot

#### 30-setup.sh

* Zsh
* WordPress
* local DNS

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

