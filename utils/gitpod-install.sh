#!/bin/bash
# This script installs fossology in Gitpod's workspace
# Copyright (C) 2021 Siemens AG
# Author: Gaurav Mishra <mishra.gaurav@siemens.com>
#
#  This program is free software; you can redistribute it and/or
#  modify it under the terms of the GNU General Public License
#  version 2 as published by the Free Software Foundation.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License along
#  with this program; if not, write to the Free Software Foundation, Inc.,
#  51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

# Make sure apache conf is accessible
mkdir -p /workspace/apache
chmod 777 /workspace/apache

# Install FOSSology in Gitpod's workspace
sudo make install PREFIX="/workspace/fossy/code" INITDIR="/workspace/fossy/etc" \
  REPODIR="/workspace/fossy/srv" LOCALSTATEDIR="/workspace/fossy/var" \
  APACHE2_SITE_DIR="/workspace/apache" SYSCONFDIR="/workspace/fossy/etc/fossology"

# Run postinstall script
sudo /workspace/fossy/code/lib/fossology/fo-postinstall || echo "Done"

# Fix the FOSSology path for Apache
sudo sed -i "s/\/usr\/local\/share/\/workspace\/fossy\/code\/share/" "/workspace/apache/fossology.conf"

# Fix permissions
sudo chown gitpod:fossy -R /workspace/fossy/var /workspace/fossy/etc/fossology/Db.conf
