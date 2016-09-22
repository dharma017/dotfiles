#!/bin/bash

#===============================================================================
# Hardening WordPress - Changing file permissions
##===============================================================================
# This script correct WordPress project file permissions
# based on codex recommendations from
# http://codex.wordpress.org/Hardening_WordPress#File_permissions
#===============================================================================
#
# Author: Dharma Raj Thapa <relizon44 [at] gmail [dot] com>
#
set -o nounset -o pipefail

# I typically keep this script in the root of the site
cd $(dirname $0)


#===============================================================================
# Permission Modes
#===============================================================================
# Mode	 Str Perms	  Explanation
#===============================================================================
#
# 0477	-r--rwxrwx	owner has read only (4), other and group has rwx (7)
# 0677	-rw-rwxrwx	owner has rw only(6), other and group has rwx (7)
# 0444	-r--r--r--	all have read only (4)
# 0666	-rw-rw-rw-	all have rw only (6)
# 0400	-r--------	owner has read only(4), group and others have no permission(0)
# 0600	-rw-------	owner has rw only, group and others have no permission
# 0470	-r--rwx---	owner has read only, group has rwx, others have no permission
# 0407	-r-----rwx	owner has read only, other has rwx, group has no permission
# 0670	-rw-rwx---	owner has rw only, group has rwx, others have no permission
# 0607	-rw----rwx	owner has rw only, group has no permission and others have rwx
#
#===============================================================================
# Settings
#===============================================================================

# example path: /var/www/foo
echo -n "Wordpress Project full path ? "
read -e WP_ROOT

# Check the settings have been filled in above
if [ -z "$WP_ROOT" ]; then
	echo "This script has not been configured correctly." >&2
	exit 1
fi

# Check if project directory exists
if [ -d "$WP_ROOT" ]
then
    echo "Directory $WP_ROOT exists."
else
    echo "Error: Directory $WP_ROOT does not exists."
    exit 1
fi

# Check if directory exists is wordpress project
if [ -f "$WP_ROOT/wp-config.php" ]
then
    echo "Directory $WP_ROOT is wordpress project."
else
    echo "Error: Directory $WP_ROOT is not wordpress project."
    exit 1
fi

echo -n "Are you sure you want to set permission for this project ? [yes|no] "
read -e ANSWER

if [ $ANSWER = 'yes' ]
then
    echo "Changing ownership..."
    sudo chown -R $USER:$USER $WP_ROOT || exit
      
    # All directories should be 755 or 750
    # No directories should ever be given 777, even upload directories.
    # Since the php process is running as the owner of the files,
    # it gets the owners permissions and can write to even a 755 directory.
    echo "Setting permission to directory type..."
    find $WP_ROOT/ -type d -exec chmod 755 {} + || exit
    
    # All files should be 644 or 640
    echo "Setting permission to files type..."
    find $WP_ROOT/ -type f -exec chmod 644 {} + || exit
    
    # Exception: wp-config.php should be 440 or 400 to prevent other users on the server from reading it
    echo "Setting permission to wordpress config file..."
    chmod 440 $WP_ROOT/wp-config.php || exit
    
    # 644 > 604
    # The bit allowing the group owner of the .htaccess file read permission was removed.
    # 644 is normally required and recommended for .htaccess files.
    echo "Setting permission to apache config file..."
    chmod 644 $WP_ROOT/.htaccess  || exit
fi

# Done
echo "Done."