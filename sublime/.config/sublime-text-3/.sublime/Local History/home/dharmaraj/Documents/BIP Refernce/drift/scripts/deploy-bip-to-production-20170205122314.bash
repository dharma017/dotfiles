#!/bin/bash

SOURCE_DIR="/tmp/deploy"

GIT_URL="git@gitlab.websearchpro.net:php/bip.git"
VHOST_DIR="/var/www/barninternetprojektet.se"

DEPLOY_DIR="${VHOST_DIR}/current"

cd ${VHOST_DIR}

echo "Stopping Apache service"
systemctl stop httpd.service

[[ -d "${DEPLOY_DIR}" ]] && runuser -g apache -u apache -- rm -v -fr "${DEPLOY_DIR}"

runuser -g apache -u apache -- cp -v -rf "${SOURCE_DIR}/current/" "${VHOST_DIR}"

runuser -g apache -u apache -- ln -v -snrf "${VHOST_DIR}/shared/cache" "${DEPLOY_DIR}/cache"
runuser -g apache -u apache -- ln -v -snrf "${VHOST_DIR}/shared/application/cache" "${DEPLOY_DIR}/application/cache"
runuser -g apache -u apache -- ln -v -snrf "${VHOST_DIR}/shared/application/logs" "${DEPLOY_DIR}/application/logs"
runuser -g apache -u apache -- ln -v -snrf "${VHOST_DIR}/shared/images/uploads" "${DEPLOY_DIR}/images/uploads"

runuser -g apache -u apache -- ln -v -snrf "${VHOST_DIR}/shared/assets/sound_files" "${DEPLOY_DIR}/assets/sound_files"

runuser -g apache -u apache -- cp -v -f /home/bjosod/project/database.php "${DEPLOY_DIR}/application/config/"

runuser -g apache -u apache -- cp -v -f /home/bjosod/project/.htaccess "${DEPLOY_DIR}"

runuser -g apache -u apache -- chmod -R 777 "${VHOST_DIR}/shared/"

echo
echo -n "Do you want to perform local backup (database only) ? [no|yes] "
read -e DUMP_DATABASE
DUMP_DATABASE=${DUMP_DATABASE:-no}

if [ $DUMP_DATABASE = 'yes' ]
then

	systemctl stop httpd.service

	echo "Dumping database..."
	/drift/scripts/dump_mariadb.bash
    
fi

echo "Starting Apache service"
systemctl start httpd.service

echo "Done"