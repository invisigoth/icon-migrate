<?php

/**
 * Prerequisite: settings are in settings.post.php
 * Run php -dsafe_mode=Off icon_migrate_post.php
 */
require_once './settings.post.php';

$time_start = microtime(true);
$json_dir = $settings['json_dir'];
$list_jsons = scandir($json_dir);
$json_total = count($list_jsons);
$failure = 0;
$processed = 0;

foreach ($list_jsons as $json_index => $json) {
  if ($json == '.' || $json == '..' || $json == '.DS_Store') {
    continue;
  }
  $jsonStr = file_get_contents($json_dir . "/$json");
  if (trim($jsonStr) == "") {
    continue;
  }
  $jsonStr = str_replace("'", "&quot;", $jsonStr);
  $progress = round(100 * ($json_index/$json_total));
  echo 'Processing: ' . $json . ' (' . $progress . "%)\n";
  $processed++;
  $curl_result = post_json_drupal($jsonStr, $json, $settings['api_url'], $settings['user'], $settings['pass']);
  if (!$curl_result) {
    $failure++;
  }
}

echo "\n==============================================";
echo "\n== Attempted to processed: " . $processed . " pages, $failure failed.\n";

// Print running time.
$time_end = microtime(true);
$execution_time = round(($time_end - $time_start), 2);
echo "Time elapsed: " . $execution_time . " second(s)\n\n";
/**
 * Execute the curl command line to post json to Drupal.
 * @param  string  $json [encoded json string]
 * @return boolean
 * php -dsafe_mode=Off post_drupal_curl.php dfs.ny.gov 2 > /Sites/migration/print.txt
 */

function post_json_drupal($json, $json_file_name, $api_url, $api_user, $api_pass) {
  $execStr = <<<EOF
  curl --insecure \
   --include \
   --request POST \
   --user '$api_user:$api_pass' \
   --header 'Content-type: application/hal+json' \
   $api_url \
   --data-binary '$json' \
   2> /dev/null
EOF;
  if (exec($execStr)) {
    return TRUE;
  }
  else {
    print "Error occurred whilst processing: $json_file_name\n";
    return FALSE;
  }
}
