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

newstep "Server Stack provision : $provider"

function do_mysql {
	# MySQL
	#
	# Use debconf-set-selections to specify the default password for the root MySQL
	# account. This runs on every provision, even if MySQL has been installed. If
	# MySQL is already installed, it will not affect anything. 
	echo percona-server-server percona-server-server/root_password password root | debconf-set-selections
	echo percona-server-server percona-server-server/root_password_again password root | debconf-set-selections

	# services
	newstep "Percona Server (MySQL) Configuration"
	if [ ! -f /etc/mysql/my.cnf ]; then
	#	mv /etc/mysql/my.cnf /etc/mysql/my.cnf-backup
	  echo -e "${list} my.cnf setup"
		unlink /etc/mysql/my.cnf
		cp /vagrant/config/mysql/my.cnf /etc/mysql/my.cnf
		echo -e "${list} Restart service"
		service mysql restart
		mysql -u root -e "CREATE FUNCTION fnv1a_64 RETURNS INTEGER SONAME 'libfnv1a_udf.so'"
		mysql -u root -e "CREATE FUNCTION fnv_64 RETURNS INTEGER SONAME 'libfnv_udf.so'"
		mysql -u root -e "CREATE FUNCTION murmur_hash RETURNS INTEGER SONAME 'libmurmur_udf.so'"
	fi
	if [ -f /vagrant/database/init-custom.sql ]
	then
	  # Create the databases (unique to system) that will be imported with
	  # the mysqldump files located in database/backups/
	  echo -e "${list} Custom MySQL setup..."
		mysql -u root < /vagrant/database/init-custom.sql
	else
	  # Setup MySQL by importing an init file that creates necessary
	  # users and databases that our vagrant setup relies on.
	  echo -e "${list} Default MySQL setup.."
	  mysql -u root < /vagrant/database/init.sql
	fi
	# Process each mysqldump SQL file in database/backups to import 
	# an initial data set for MySQL.
	#/srv/database/import-sql.sh
}

function do_php5conf {
	newstep "PHP5 configuration"
	echo -e "${list} Disable xdebug"
	php5dismod xdebug
	echo -e "${list} pool.d/www.conf"
	unlink /etc/php5/fpm/pool.d/www.conf
	cp /vagrant/config/php5/poold-www.conf /etc/php5/fpm/pool.d/www.conf
	#echo -e "${list} conf.d/php-custom.ini"
	#ln -sf /vagrant/config/php5/php-custom.ini /etc/php5/fpm/conf.d/php-custom.ini
	#echo -e "${list} conf.d/xdebug.ini"
	#ln -sf /vagrant/config/php5/xdebug.ini /etc/php5/fpm/conf.d/xdebug.ini
	#echo -e "${list} conf.d/apc.ini"
	#ln -sf /vagrant/config/php5/apc.ini /etc/php5/fpm/conf.d/apc.ini
}

function do_nginx {

	newstep "Nginx configuration"
	
	rm /etc/nginx/sites-available/default*

	if [ ! -f /etc/nginx/nginx-wp-common.conf ]; then
		echo -e "${list} /etc/nginx/nginx.conf"
	  cp /etc/nginx/nginx.conf /etc/nginx/nginx.conf-backup
	  cp /vagrant/config/nginx/nginx.conf /etc/nginx/nginx.conf

		echo -e "${list} /etc/nginx/nginx-wp-common.conf"
	  cp /vagrant/config/nginx/nginx-wp-common.conf /etc/nginx/nginx-wp-common.conf
		
		echo -e "${list} /etc/nginx/custom-sites"
		case $provider in
			'virtualbox')
		  cp /vagrant/config/nginx/sites-available/local-wordpress.conf /etc/nginx/sites-available/wordpress.conf
			;;
			'digital_ocean')
		  cp /vagrant/config/nginx/sites-available/remote-wordpress.conf /etc/nginx/sites-available/wordpress.conf
			;;
		esac
		ln -s /etc/nginx/sites-available/wordpress.conf /etc/nginx/sites-enabled/wordpress.conf
		
	else
	  echo -e "${pass} Nothing to do."
	fi
	if [ ! -e /etc/nginx/server.key ]; then
	  echo -e "${list} Generate Nginx server private key..."
	  vvvgenrsa=`openssl genrsa -out /etc/nginx/server.key 2048 2>&1`
	  echo $vvvgenrsa
	fi
	if [ ! -e /etc/nginx/server.csr ]; then
	  echo -e "${list} Generate Certificate Signing Request (CSR)..."
	  openssl req -new -batch -key /etc/nginx/server.key -out /etc/nginx/server.csr
	fi
	if [ ! -e /etc/nginx/server.crt ]; then
	  echo -e "${list} Sign the certificate using the above private key and CSR..."
	  vvvsigncert=`openssl x509 -req -days 365 -in /etc/nginx/server.csr -signkey /etc/nginx/server.key -out /etc/nginx/server.crt 2>&1`
	  echo $vvvsigncert
	fi
}


function do_varnish {
	newstep "Varnish setup"
	if [ ! -f /etc/varnish/wordpress.vcl ]; then
		echo -e "${list} Default Varnish setup"
		cp /vagrant/config/varnish/default_varnish /etc/default/varnish
		echo -e "${list} WordPress VCL"
		cp /vagrant/config/varnish/wordpress.vcl /etc/varnish/wordpress.vcl
	else
	  echo -e "${pass} Nothing to do."
	fi
}


function do_utils {
	newstep "Utilities setup"
	export COMPOSER_HOME="/opt/composer";
	if which composer &>/dev/null;
	then
		echo -e "${list} Updating Composer.."
		composer self-update
	else
		echo -e "${list} Installing Composer.."
		curl -sS https://getcomposer.org/installer | php
		chmod +x composer.phar
		mv composer.phar /usr/local/bin/composer
	fi
	composer --version
	composer global require wp-cli/wp-cli=0.13.0
}


function clean_system {
	# cleaning
	newstep "Housekeeping and service restart"
	echo -e "${list} APT cache cleaning"
	apt-get autoclean
	apt-get autoremove
	rm -f /var/cache/apt/archives/*.deb
	echo -e "${list} Restarting services.."
	service memcached restart
	service mysql restart
	service php5-fpm restart
	service nginx restart
	service varnish restart
}


do_mysql
do_php5conf
do_nginx
do_varnish
do_utils
clean_system