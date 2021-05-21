<?php
# Checks Moodledata 'filedir' directory for files not in the database
# (mdl_files).  These files can probably be removed.
# Run this script from the Moodle source code directory, e.g.:
#   cd /path/to/moodle
#   php /path/to/moodledata_orphans.php
# Options -b (bare output) and -p (show full path) list the full path of each
# orphaned file with no other messages, output which is suitable for piping to
# other commands.

define('WARNING_TXT', 'warning.txt');
define('CLI_SCRIPT', true);
require('config.php');
require_once($CFG->libdir . '/dml/moodle_database.php');
global $DB;

$bare_output = array_search("-b", $argv) ? true : false;
$full_path = array_search("-p", $argv) ? true : false;

$filedir = $CFG->dataroot . DIRECTORY_SEPARATOR . 'filedir';
$warning_txt = $filedir . DIRECTORY_SEPARATOR . WARNING_TXT;
$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($filedir));

foreach ($objects as $name => $object) {
	if (is_file($name) && ($name !== $warning_txt)) {
		if (!$DB->record_exists('files',
					array('contenthash' => basename($name)))) {
			if ($bare_output) {
				$s = "";
			} else {
				$s = "Not in database: ";
			}

			if ($full_path) {
				$s = $s . $name;
			} else {
				$s = $s . substr($name, strlen($filedir) + 1);
			}

			echo $s . PHP_EOL;
		}
	}
}
