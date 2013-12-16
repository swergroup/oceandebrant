#!/bin/bash

do_install(){
	wp core config
	wp core install
	wp theme activate roots
	wp plugin activate pods
	wp plugin activate uploadplus
	wp plugin activate wordpress-importer
	wp plugin activate w3-total-cache
}

do_update(){
	wp core update
	wp core update-db
	wp plugin update --all
	wp theme update --all
}

while :
do
	case "$1"
	in
		-i | --install)
			do_install
			exit 0
			;;
		-u | --update)
			do_update
			exit 0
			;;
		-h | --help)
		-*)
			echo "Usage: $0 [-i|--install] [-u|--update] "
			exit 0
			;;
		--)
			shift
			break
			;;
		*)
			break
			;;
	esac

	default
	in
	esac
done