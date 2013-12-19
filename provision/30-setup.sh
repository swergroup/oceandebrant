#!/bin/bash

provider=$1

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

do_zsh(){
	newstep "Zsh setup"
	if [ ! -f /etc/skel/.zshrc ]; then
		echo -e "${list} Fetch GRML.org Zsh configuration .."
		wget -q -O /etc/skel/.zshrc http://git.grml.org/f/grml-etc-core/etc/zsh/zshrc
	fi
	if [ ! -f /home/vagrant/.zshrc ]; then
		echo -e "${list} Update vagrant shell .."
		cp /etc/skel/.zshrc /home/vagrant/.zshrc
		chown vagrant:vagrant /home/vagrant/.zshrc
		echo 'export PATH="/opt/composer/vendor/bin/:$PATH" ' >> /home/vagrant/.zshrc
		usermod -s /usr/bin/zsh vagrant
	fi
	if [ ! -f /root/.zshrc ]; then
		echo -e "${list} Update root shell .."
		cp /etc/skel/.zshrc /root/.zshrc
		echo 'export PATH="/opt/composer/vendor/bin/:$PATH" ' >> /root/.zshrc
		usermod -s /usr/bin/zsh root
	fi
}

do_wordpress(){
	newstep "WordPress setup"

	if [ ! -f /srv/www/wordpress/composer.json ]; then
		echo -e "${list} First WordPress setup"
		mkdir /srv/www/wordpress
		if [ ! -f /srv/www/wordpress/composer.json ]; then
			cp /vagrant/config/wordpress/composer.json /srv/www/wordpress/composer.json
			cp /vagrant/config/wordpress/wp-cli-local.yml /srv/www/wordpress/wp-cli.yml
			cd /srv/www/wordpress
			export COMPOSER_HOME="/opt/composer";
			composer install
		fi
	else
		cd /srv/www/wordpress
		echo -e "${list} WordPress Update"
		composer selfupdate
		composer update
	fi
}

do_dns(){
	newstep "DNS update"
	echo "127.0.0.1 $1" >> /etc/hosts
	echo "nameserver 127.0.0.1" >> /etc/resolv.conf
}


case $provider in
	'virtualbox' | *)
		newstep "Virtualbox configuration"
		do_zsh
		do_wordpress
		do_dns 'dev.oceandebrant.wp'
		;;
	'digital_ocean')
		newstep "Virtualbox configuration"
		do_zsh
		do_wordpress
		do_dns 'remote.oceandebrant.wp'
		;;
esac