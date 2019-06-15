Moodle utilities
================

Some utilities for use with Moodle.

  * `moodledata_orphans.php`: List files in Moodledata `filedir` directory that aren't in the Moodle database.
  * `new_test_site`: Create a new Moodle test site.

## moodledata\_orphans.php
List files in Moodledata `filedir` directory that aren't in the Moodle database.

Moodle stores course files in the `filedir` directory renamed to the file's content hash so that the same file is only stored once with each instance referenced in the `mdl_files` table. When there are no references left the file should be deleted. In some cases a file can exist in `filedir` without a reference in `mdl_files` in which case Moodle doesn't know it's there and is not using it. In this case the file can almost certainly be removed. This script lists these files.

It must be run from the Moodle source code directory.

Warning: Although the listed files can probably be deleted it is recommended that you backup these files beforehand.

### Usage:

	cd /path/to/moodle
	php -f /path/to/moodledata_orphans.php

## new\_test\_site
Create a new Moodle test site.

Assumes a CentOS 7 server with the correct version of PHP and extensions installed, and MariaDB installed and running.

This script:

  * Creates a database for the site based on the site domain name, e.g. `moodle1_example_com`.
  * Creates a database user for the site with the same name as the database, e.g. `moodle1_example_com`.
  * Creates the site in `/var/www` using the site domain name, e.g. `/var/www/moodle1.example.com`.
  * Downloads the Moodle source using `git clone`. It looks for a local reference repository in `/usr/share/repos/moodle` and uses this if present.
  * Sets the SELinux context for the Moodledata directory (`httpd_sys_rw_content_t`).
  * Runs the Moodle install script, `admin/cli/install.php`.
  * Creates a self-signed certificate and key pair for the site domain name, e.g. `/etc/pki/tls/certs/moodle1.example.com.crt` and `/etc/pki/tls/private/moodle1.example.com.key`.
  * Creates an Apache config file in `/etc/httpd/conf.d` using the site domain name, e.g. `moodle1.example.com.conf`.

The site's `admin` user has the password `Password-1`.

You then need to add DNS records or `hosts` file entries in order to access the site.

### Usage:
new\_test\_site [[-b branch-ver] | [ -t tag]] [-g git-repo] domain-name

-b branch-ver: two-digit branch to use, e.g. 31 which corresponds to `MOODLE_31_STABLE`. Cannot be used if -t is used.

-t tag: Git tag corresponding to the naming scheme Moodle uses for specific versions, e.g. `v3.5.6`. Cannot be used if -b is used

-g git-repo: Git repository to use. By default `git://git.moodle.org/moodle.git` is used, but if this port is blocked, this option allows an alternative, e.g. `https://github.com/moodle/moodle.git`.

### Examples:
New test site:

	new_test_site moodle1.example.com

New Moodle 3.5 test site using the alternative GitHub repo:

	new_test_site -b 35 -g https://github.com/moodle/moodle.git moodle2.example.com

New Moodle 3.6.2 test site:

	new_test_site -t v3.6.2 moodle3.example.com
