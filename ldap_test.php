<?php
// A script to help identify and troubleshoot the settings for LDAP
// authentication in Moodle.
//
// IMPORTANT: You must define the constants below with the values to test.
// These are the values that will go in the Moodle LDAP Server authentication
// configuration page.

// Address of the LDAP server, e.g. 'ldap://ldap1.example.corp' or
// 'ldap://192.168.0.42'.  For Active Directory use the AD domain name e.g.
// 'ldap://example.corp'.
// define('HOST_URL', 'ldap://ldap1.example.com');

// Bind credentials for querying the LDAP server.  Some servers allow
// this to be done anonymously in which case these should be blank.  BIND_DN
// should be specified in the LDAP name format ('CN=...,DC=...,DC=...').  For
// Active Directory this can be the the Win2000-format name, e.g.
// 'josmith@exampl.corp'.
// define('BIND_DN', 'cn=ldapuser,dc=example,dc=corp');
// define('BIND_PW', 'yourpassword');

// Location in the LDAP hierarchy where your users are located.  Active
// Directory has a Users container which would be specified with
// 'cn=Users,dc=example,dc=corp'.
// define('CONTEXTS', 'dc=example,dc=com');

// Filter for your users.  LDAP can store users, groups, computers and more.
// Only users are required and this is specified here but the setting is
// specific to the LDAP server type '(objectClass=user)' or
// '(objectClass=posixAccount)' are common examples.  For Active Directory use
// '(objectClass=user)'.
// define('FILTERS', '(objectClass=user)');

// The user fields to obtain from the LDAP server.  Moodle requires a unique
// user value for the User Attribute field.  You can optionally read other
// values like first name, last name, email address, etc.  The specific field
// names can vary with LDAP server types.  For Active Directory try
// 'sAMAccountName, givenName, sn, mail'.
// define('ATTRIBUTES', 'uid, cn, mail');

// Example settings for public test LDAP server ldap.forumsys.com.  See
// http://www.forumsys.com/tutorials/integration-how-to/ldap/online-ldap-test-server
// for details.
// define('HOST_URL', 'ldap://ldap.forumsys.com');
// define('BIND_DN', 'cn=read-only-admin,dc=example,dc=com');
// define('BIND_PW', 'password');
// define('CONTEXTS', 'dc=example,dc=com');
// define('FILTERS', '(objectClass=person)');
// define('ATTRIBUTES', 'uid, cn, mail');

// Start TLS is recommended and preferred over LDAPS.  Active Directory
// supports this.  Change it to false to disable this.
define('STARTTLS', true);

if (!defined('HOST_URL')) {
	die("Error: Constants HOST_URL, BIND_DN, etc. must be defined." . PHP_EOL);
}

$ldapconn = ldap_connect(HOST_URL) or
			die("Error calling ldap_connect()." . PHP_EOL);

ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3) or
			die("Error using ldap_set_option to set LDAP_OPT_PROTOCOL_VERSION" .
			PHP_EOL);

 // Needed for Active Directory, probably OK for others?
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0) or
			die("Error using ldap_set_option to set LDAP_OPT_REFERRALS" .
			PHP_EOL);

ldap_set_option($ldapconn, LDAP_OPT_DEREF, 0) or
			die("Error using ldap_set_option to set LDAP_OPT_DEREF" . PHP_EOL);

if (STARTTLS) {
	ldap_start_tls($ldapconn) or die("Error calling ldap_start_tls()" .
				PHP_EOL);
}

if (!empty(BIND_DN)) {
	ldap_bind($ldapconn, BIND_DN, BIND_PW) or
				die("Error calling ldap_bind()." . PHP_EOL);
}

$attributes = preg_split("/[\s,]+/", ATTRIBUTES);
$attributes = array_map('strtolower', $attributes);
$sr = ldap_search($ldapconn, CONTEXTS, FILTERS, $attributes);

if (!$sr) {
	die("Error returned by ldap_search()." . PHP_EOL);
}

$info = ldap_get_entries($ldapconn, $sr);

if (!$info) {
	die("Error returned by ldap_get_entries()." . PHP_EOL);
}

$record_count = 0;

for ($i = 0; $i < $info['count']; $i++) {
	foreach ($attributes as $attribute) {
		echo "$attribute: ";

		if (array_key_exists($attribute, $info[$i])) {
			for ($j = 0; $j < $info[$i][$attribute]['count']; $j++) {
				if ($j > 0) {
					echo ', ';
				}

				echo $info[$i][$attribute][$j];
			}
		}

		echo PHP_EOL;
	}

	$record_count = $i;
	echo PHP_EOL;
}

echo "$record_count items returned." . PHP_EOL;
ldap_close($ldapconn);
