#!/bin/bash

function do_utils {
	newstep "Utilities setup"
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

	if [ ! -d /srv/www/wp-cli ]
	then
	  echo -e "${list} Cloning wp-cli repository"
		git clone git://github.com/wp-cli/wp-cli.git /srv/www/wp-cli
		cd /srv/www/wp-cli
	  echo -e "${list} Installing wp-cli"
		composer install
	  echo -e "${list} Installing wp-cli community packages"
		composer config repositories.wp-cli composer http://wp-cli.org/package-index/
		for pack in "${wpcli_packages[@]}"
		do
			echo -e "  ${list} $pack"
		  composer require $pack &>/dev/null
		done
	else
	  echo -e "${list} Updating wp-cli"
		cd /srv/www/wp-cli
		composer update
	fi
	echo -e "${list} wp-cli symlink"
	ln -sf /srv/www/wp-cli/bin/wp /usr/local/bin/wp
	wp --info
}



export DEBIAN_FRONTEND=noninteractive

do_utils