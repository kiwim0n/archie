#!/bin/bash
# Settings
ABS_PATH=$(cd `dirname "${BASH_SOURCE[0]}"` && pwd)
#CONTENT=$ARGV[0]
#DATE=`date +%F`

if [ ! -f ${ABS_PATH}/database.auth ] 
then
 echo "ERROR: Unable to find ${ABS_PATH}/database.auth file, please create it and try again"
 exit 1
fi

if [ -f ${ABS_PATH}/../config/settings.php ]
then
 echo "ERROR: Archie appears to already be installed, settings.php exists, stopping."
 exit 1
fi

echo "Creating [archie] MySQL Database..."
echo "create database archie;" | mysql --defaults-extra-file=${ABS_PATH}/database.auth
echo "  echo \"create database archie;\" \| mysql --defaults-extra-file=${ABS_PATH}/database.auth"
echo "Inserting Base Database"
mysql --defaults-extra-file=${ABS_PATH}/database.auth archie < ${ABS_PATH}/../config/database.sql
echo "  mysql --defaults-extra-file=${ABS_PATH}/database.auth archie < ${ABS_PATH}/../config/database.sql"
echo "Inserting Initial Admin User"
mysql --defaults-extra-file=${ABS_PATH}/database.auth archie < ${ABS_PATH}/../config/install.sql
echo "  mysql --defaults-extra-file=${ABS_PATH}/database.auth archie < ${ABS_PATH}/../config/install.sql"
echo "Copying ${ABS_PATH}/../config/settings.php.dist to settings.php"
cp ${ABS_PATH}/../config/settings.php.dist ${ABS_PATH}/../config/settings.php
echo "  cp ${ABS_PATH}/../config/settings.php.dist ${ABS_PATH}/../config/settings.php"
echo "****WARNING****"
echo "Before Archie will work you must edit ${ABS_PATH}/../config/settings.php and set the proper database information and web path"
exit 0
