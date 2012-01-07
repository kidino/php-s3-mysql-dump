<?php
// MySQL settings
define('DB_SERVER','CHANGE_THIS');
define('DB_USER','CHANGE_THIS');
define('DB_PASS','CHANGE_THIS');
define('DB_DATABASE','CHANGE_THIS');

// Dump options
define('DUMP_PREFIX',''); // Define a prefix for your dump files or leave null. 
define('DUMP_TYPE','TABLES'); // Options DATABASE or TABLES  

// S3 settings
define('ACCESS_KEY','CHANGE_THIS');
define('SECRET_KEY','CHANGE_THIS'); 
define('S3_BUCKET','CHANGE_THIS'); 
define('S3_URI','CHANGE_THIS/'); // Include trailing slash for directory listings

/*
	Define tables to push to Amazon S3.
	1. $tables = '*'; 
	2. $tables = array('table1','table2','table3');
*/
$tables = '*';
?>