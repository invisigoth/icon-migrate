<?php

/**
 * Settings for icon_migrate_discovery.php
 */

define('DRUPAL_REST_LINK_HREF', 'http://govcms-ditrdc.local:88/rest/type/node/simple_content');
define('LOCAL_DIR', '/Users/jason/Sites/drupal-migrate');
define('DRUPAL_IMG_URL', '/sites/default/files/migrated');
define('DRUPAL_FILE_URL', '/sites/default/files/migrated');
define('PREFIX', ''); // "//div[@id='content']"
define('CONTENE_TYPE', 'simple_content');

define('BASE_PATH', '/Users/jason/Sites/infrastructure-gov-au');
//define('BASE_PATH', '/Users/jason/Sites/regional-gov-au');

// Where the resultant JSON files should be stored. Directory name should include a trailing slash '/'
define('JSON_DIR', '/Users/jason/Sites/icon-migrate/json/');

$summary_urls_out = '/Users/jason/Sites/icon-migrate/migration/summary.json';

// Define the container tag of the HTML fragment to be migrate
// For example use "//div[@id='text']" if the container is <div id="text">
$queries = [
  PREFIX . "//div[@id='text']",
];

$removes = array('.html', '.aspx', '.asp', '.php', BASE_PATH);

// Allowed file extensions in link href
$file_extensions = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'xlsm'];

// Keyword to taxonomy tid mapping
$terms_map = [
  '/local' => 10397,  // Local Government
  '/regional' => 10400, // Regional
  '/territories' => 10403, // Territories of Australia
  '/aviation' => 10393,
  '/cities' => 10394,
  '/department' => 10395,
  '/infrastructure' => 10396,
  '/maritime' => 10398,
  '/rail' => 10399,
  '/roads' => 10401,
  '/transport' => 10404,
  '/utilities' => 10405,
  '/vehicles' => 10406,
  '/security' => 10402
];
//Rewrite node paths for infrastructure.gov.au
$nodes_paths_custom_site1 = [
  "/roads/" => "/infrastructure-transport-vehicles/road-transport-infrastructure/",
  "/transport/programs/" => "/infrastructure-transport-vehicles/road-transport-infrastructure/",
  "/infrastructure/western_sydney/" => "/infrastructure-transport-vehicles/road-transport-infrastructure/heavy-vehicle -road-reform",
  "/aviation/"  => "/infrastructure-transport-vehicles/vehicles/aviation/",
  "/rail/" => "/infrastructure-transport-vehicles/rail",
  "/maritime/" => "/infrastructure-transport-vehicles/maritime",
  "/transport/" => "/infrastructure-transport-vehicles/transport-strategy-policy",
  "/transport/disabilities/" => "/infrastructure-transport-vehicles/transport-accessibility",
  "/cities/" => "/territories-regions-cities/cities",
  "https://www.infrastructure.gov.au/cities/" => "/territories-regions-cities",
  "/vehicles/" => "/infrastructure-transport-vehicles/vehicles",
  "/water/" => "/infrastructure-transport-vehicles/water",
  "/infrastructure/ngpd/"=>"/infrastructure-transport-vehicles/project-delivery",
  ];
//Rewrite node paths for regional.gov.au

  $nodes_paths_custom_site2 = [
  "/regional/"  => "/territories-regions-cities",
  "/territories/" => "/territories-regions-cities/territories",
  "/local/" => "/territories-regions-cities/local-government",
  ];