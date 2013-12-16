#!/bin/bash

cd /srv/www/wordpress

if [ ! -f /etc/mysql/composer.lock ];
	then
		composer install
	else
		composer selfupdate
		composer update
fi

