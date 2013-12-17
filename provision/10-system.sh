#!/bin/bash

debrant_version='0.3.1'

## Tunables

export COMPOSER_HOME="/opt/composer";

# Debian package checklist
apt_package_check_list=(
	curl
	debian-keyring
	deborphan
	dos2unix
	findutils
	gettext
	geoip-bin
	geoip-database
	git
	gnupg2
	gnupg-curl
	gnu-standards
	imagemagick
	kexec-tools
	links
	libaio1
	libdbi-perl
	libnet-daemon-perl
	libmemcache0
	libmemcached10
	libmysqlclient18=5.5.34-rel32.0-591.wheezy
	localepurge
	lynx
  mailutils
	mcrypt
	memcached
	mlocate
	nginx-extras
	ntp
	ntpdate
  nullmailer
	optipng
	percona-playback
	percona-toolkit
	percona-server-client-5.5
	percona-server-common-5.5
	percona-server-server-5.5
	percona-xtrabackup
	php-apc
	php-pear
	php5-cli
	php5-common
	php5-curl
	php5-dev
	php5-fpm
	php5-gd
	php5-geoip
	php5-imagick
	php5-imap
	php5-mcrypt
	php5-memcache
	php5-memcached
	php5-mysql
	php5-sqlite
	php5-xdebug
	php5-xmlrpc
	php5-xsl
  re2c
	rsync
	screen
	unar
	unrar
	unzip
	varnish
	vim
	wget
	zsh
)



# Text color variables
txtred='\e[0;31m'       # red
txtgrn='\e[0;32m'       # green
txtylw='\e[0;33m'       # yellow
txtblu='\e[0;34m'       # blue
txtpur='\e[0;35m'       # purple
txtcyn='\e[0;36m'       # cyan
txtwht='\e[0;37m'       # white
bldred='\e[1;31m'       # red    - Bold
bldgrn='\e[1;32m'       # green
bldylw='\e[1;33m'       # yellow
bldblu='\e[1;34m'       # blue
bldpur='\e[1;35m'       # purple
bldcyn='\e[1;36m'       # cyan
bldwht='\e[1;37m'       # white
txtund=$(tput sgr 0 1)  # Underline
txtbld=$(tput bold)     # Bold
txtrst='\e[0m'          # Text reset
txtdim='\e[2m'
# Feedback indicators
info="\n${bldblu} % ${txtrst}"
list="${bldcyn} * ${txtrst}"
pass="${bldgrn} √ ${txtrst}"
warn="${bldylw} ! ${txtrst}"
dead="${bldred}!!!${txtrst}"


function newstep {
	echo -e "${txtrst}"
	echo -e "${bldblu}###${txtrst} ${bldwht}$1${txtrst}"
	echo -e "${txtrst}"
}

function do_apt {
	newstep "APT sources"
	if [ -f /etc/apt/sources.list.d/grml.list ]; then
		sudo rm /etc/apt/sources.list.d/grml.list
	fi

  echo -e "${list} GPG keys setup"
	# percona server (mysql)
	apt-key adv --keyserver keys.gnupg.net --recv-keys 1C4CBDCDCD2EFD2A	2>&1 > /dev/null
	
	#grml
	apt-key adv --keyserver subkeys.pgp.net --recv-keys F61E2E7CECDEA787	2>&1 > /dev/null
	
	# varnish
	wget -qO- http://repo.varnish-cache.org/debian/GPG-key.txt | apt-key add -

  echo -e "${list} sources.list"
	unlink /etc/apt/sources.list
	cp /vagrant/config/apt/sources.list /etc/apt/sources.list
	apt-get update --assume-yes

	newstep "System packages"
	for pkg in "${apt_package_check_list[@]}"
	do
		if dpkg -s $pkg 2>&1 | grep -q 'Status: install ok installed';
		then 
			echo -e "${pass} $pkg"
		else
			echo -e "${warn} $pkg"
			apt_package_install_list+=($pkg)
		fi
	done
	if [ ${#apt_package_install_list[@]} = 0 ];
	then 
	  echo -e "${pass} Nothing to do."
	else
	  echo -e "${list} Installing packages.."
		aptitude purge ~c -y
		apt-get install --force-yes --assume-yes ${apt_package_install_list[@]}
		apt-get clean
	fi
}

function main_footer {
	cp /vagrant/config/server.tag /etc/motd
	echo $debrant_version > /etc/oceandebrant-version
}

export DEBIAN_FRONTEND=noninteractive

cat /vagrant/config/server.tag
do_apt
main_footer
