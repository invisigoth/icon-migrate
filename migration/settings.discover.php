<?php

/**
 * Settings for icon_migrate_discovery.php
 */

define('DRUPAL_REST_LINK_HREF', 'http://drupal-migrate.local/rest/type/node/page');
define('LOCAL_DIR', '/Users/jason/Sites/drupal-migrate');
define('DRUPAL_IMG_URL', '/sites/default/files/images');
define('DRUPAL_FILE_URL', '/sites/default/files/documents');
define('PREFIX', ''); // "//div[@id='content']"
define('CONTENE_TYPE', 'page');

define('PAGE_QUALIFIER', '.aspx.html');
define('BASE_PATH', '/Users/jason/Sites/regional-gov-au');


// Where the resultant JSON files should be stored. Directory name should include a trailing slash '/'
define('JSON_DIR', '/Users/jason/Sites/icon-migrate/migration/json/');

$summary_urls_out = '/Users/jason/Sites/icon-migrate/migration/summary.json';

// Define the container tag of the HTML fragment to be migrate
// For example use "//div[@id='text']" if the container is <div id="text">
$queries = [
  PREFIX . "//div[@id='text']",
];

$removes = array('/index', '.html', '.aspx', '.asp', '.php', BASE_PATH);

// Allowed file extensions in link href
$file_extensions = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'xlsm'];

// Keyword to taxonomy tid mapping
$terms_map = [
  '/local' => '2',  // Local Government
  '/regional' => '1', // Regional
  '/territories' => '3', // Territories of Australia
  ];
