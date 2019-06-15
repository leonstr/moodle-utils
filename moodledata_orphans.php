<?php
# Checks Moodledata 'filedir' directory for files not in the database
# (mdl_files).  These files can probably be removed.
# Run this script from the Moodle source code directory, e.g.:
#   cd /path/to/moodle
#   php -f /path/to/moodledata_orphans.php

define('WARNING_TXT', 'warning.txt');
define('CLI_SCRIPT', true);
require('config.php');
require_once($CFG->libdir . '/dml/moodle_database.php');
global $DB;

$filedir = $CFG->dataroot . DIRECTORY_SEPARATOR . 'filedir';
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($filedir));

foreach ($objects as $name => $object) {
	if (is_file($name) && ($name !== $filedir . DIRECTORY_SEPARATOR .
					WARNING_TXT)) {
		if (!$DB->record_exists('files',
					array('contenthash' => basename($name)))) {
			echo "Not in database: " . substr($name, strlen($filedir) + 1) .
						PHP_EOL;
		}
	}
}
