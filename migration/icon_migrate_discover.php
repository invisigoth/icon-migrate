<?php

/**
 * Prerequisite: settings are in settings.discover.php
 * Run: php -f icon_migrate_discover.php
 */

$time_start = microtime(true);

require_once './settings.discover.php';

$processed_pages = [];
$pages_json = [];

$directory = new RecursiveDirectoryIterator(BASE_PATH);
$iterator = new RecursiveIteratorIterator($directory);

foreach ($iterator as $fileinfo) {
    $file_path = $fileinfo->getPathname();
    if (strstr($file_path, PAGE_QUALIFIER) || strstr($file_path, 'index.html')) {
        _crawl_page($file_path);
    }
}

file_put_contents($summary_urls_out, json_encode($processed_pages));
_file_save_json($pages_json);


// Job summary
echo "\n==============================================";
echo "\n== Pages discovered:                     " . count($processed_pages);
echo "\n== Qualified pages convereted to JSON:   " . count($pages_json);
$time_end = microtime(TRUE);
$execution_time = round(($time_end - $time_start), 2);
echo "\nTime elapsed: " . $execution_time . " second(s)\n\n";


/**
 * Output array data to JSON files.
 * @param  array $pages_json
 * @return void
 */

function _file_save_json(&$pages_json) {

    foreach ($pages_json as $page_index => $json) {
        $name = "page_$page_index.json";
        $json_data = json_encode($json, JSON_UNESCAPED_SLASHES);

        if (!file_put_contents(JSON_DIR . "$name", $json_data)) {
            print "BAD: $name\n";
        }

    }
    return;
}

/**
 * Crawl pages recursively.
 */
function _crawl_page($path) {
    global $processed_pages;
    $processed_pages[$path] = TRUE;

    // Call to generate new content and store data in associat array.
    $dom = new DOMDocument('1.0');
    // We don't want to bother with white spaces.
    $dom->preserveWhiteSpace = FALSE;
    // Most HTML Developers are chimps and produce invalid markup...
    $dom->strictErrorChecking = FALSE;
    $dom->recover = TRUE;

    @$dom->loadHTMLFile($path);

    parse_webpage_content($path, $GLOBALS['pages_json'], $dom);

}

/**
 * Parse HTML between <div id="your_id_here">Your content</div>.
 */
function parse_webpage_content($path, &$pages_json, &$doc) {
    global $queries;
    global $removes;

    $title = get_dom_title($doc);

    // Set URL alias of the page to be migrated.
    // - The alias should not contain any file type extensions
    // - After extensions are removed, if the alias is /foo/bar/index, set it
    //   to /foo/bar
    $url_alias = parse_url($path, PHP_URL_PATH);
    if (!$url_alias || $url_alias == "/" || $url_alias == '/index.html') {
        return;
    }

    $url_alias = str_replace($removes, '', $path);

    print "Processing: $path --> $url_alias\n";

    // print "parse_web: url=$url\n urlAlians=$url_alias\n";
    $xpath = new DOMXPath($doc);
    //print_r($xpath->document);
    // $queries var in settings.crawl.php
    foreach ($queries as $query) {
        $result = $xpath->query($query);
        if (!$result || $result->length <= 0) {
            //print_r($result->length);
            continue;
        }
        else {
            // print "xpath:: $query\n" ;
            // print_r($result);
            foreach ($result as $node) {
                // print_r($node);
                $html_body = $doc->saveHTML($node);
                $html_body = html_body_clean($html_body, $url_alias);
                $termID = get_term_id($url_alias);

                // Handle situation such as /foo/bar/index.html and make the alias simply /foo/bar
                $url_alias = str_replace('/index', '/', $url_alias);

                $pages_json[] = [
                    '_links' => ['type' => ['href' => DRUPAL_REST_LINK_HREF]],
                    'title' => [['value' => $title]],
                    'type' => [['target_id' => CONTENE_TYPE]],
                    'path' => [['alias' => $url_alias]],
                    'body' => [['value' => $html_body, 'format' => 'full_html']],
                    'field_topics' => [['target_id' => $termID, 'target_type' => 'taxonomy_term']]
                ];
            }
        }
    }
}


/**
 * Get the TermID from the MAP array().
 * @param  string $url [description]
 * @return string $id   [term ID]
 */
function get_term_id($path) {
    global $terms_map;

    $path_dir = pathinfo($path, PATHINFO_DIRNAME);

    foreach ($terms_map as $key => $termId) {
        if (strpos($path_dir, $key) === 0) {
            // print "$key => $termId\n";
            return $termId;
        }
    }

    return $terms_map['default'];
}

/**
 * Get the Dom page title.
 * @param  DOMDucument $dom [description]
 * @return string      [description]
 */
function get_dom_title(&$dom) {
    $list = $dom->getElementsByTagName("title");
    if ($list->length > 0) {
        return $list->item(0)->textContent;
    }
    return FALSE;
}

/**
 * Replace old link href with new href.
 * @param  string $content [html content]
 * @return string          [html content]
 */
function html_body_clean($html_body, $url_alias) {
    // print "/*** urlCur=$urlCur ***/\n";
    $html_body_dom = new DOMDocument();
    // print "#,";
    @$html_body_dom->loadHTML($html_body);
    // print_r($docDOM);
    // print "### Before ###\n content\n";
    $html_body = get_elements($html_body_dom, $html_body, 'a', 'href', $url_alias);
    // print "### After ###\n content\n";
    $html_body = get_elements($html_body_dom, $html_body, 'img', 'src', $url_alias);

    return $html_body;
}

/**
 * Replace old tag attributes  with new.
 * @param string $content [html content] *
 * @return string  [html content ]
 */
function get_elements(&$html_body_dom, &$content, $tag, $attribut, $url_alias) {
    global $file_extensions;
    global $removes;


    $current_page_url_alias = $url_alias;
    $url_alias = explode('/', $url_alias);

    if (is_array($url_alias)) {
        array_pop($url_alias);
        $url_alias = implode('/', $url_alias);
    } else {
        $url_alias = '';
    }

    $file_links = [];
    $file_links_new = [];

    $items = $html_body_dom->getElementsByTagName($tag);

    foreach ($items as $item_index => $item) {
        $file_ref = trim($item->getAttribute($attribut));
        if ($tag == 'a') {
            if (!strstr($file_ref, 'http://') && !strstr($file_ref, 'https://')) {

                print "     old: ".$current_page_url_alias.' --- '.$file_ref."\n";

                if (strstr($file_ref, PAGE_QUALIFIER) || strstr($file_ref, 'index.html') ) {
                    //  1. We will try to re-link internal cross-reference links within the migrated site
                    //  TODO: we should try to use file_exists to check the source html files
                    $file_links[] = $file_ref; // initial value
                    $new_href = str_replace($removes, '', $file_ref); // initial value
                    if (strstr($file_ref, '../')) {
                        $ref_segments = explode('/', $file_ref);
                        $double_dot_count = array_count_values($ref_segments);
                        $double_dot_count = $double_dot_count['..'];
                        if ($double_dot_count > 0) {
                            $double_dot_count++;
                            $current_page_url_segments = explode('/', $current_page_url_alias);
                            $current_page_url_segments_count = count($current_page_url_segments);
                            for ($i = 1; $i <= $double_dot_count; $i++) {
                                unset($current_page_url_segments[$current_page_url_segments_count - $i]);
                            }
                            $file_ref = str_replace('../', '', $file_ref);
                            $new_href = implode('/', $current_page_url_segments). '/' . $file_ref;
                        }
                        if (file_exists(BASE_PATH.$new_href)) {
                            $new_href = str_replace($removes, '', $new_href);
                        }
                    }

                    if ((substr($current_page_url_alias, -1) != '/') && (substr($new_href, 0,1) != '/')) {
                        $new_href = $current_page_url_alias.'/'.$new_href;
                    }

                    $file_links_new[] = $new_href;
                    print "     new: ".$new_href."\n";

                } else {
                    // 2. We will try to re-link an internal file (document) within the migrated site
                    //
                    $href_extension = end(explode('.', $file_ref));
                    if (in_array(strtolower($href_extension), $file_extensions)) {
                        if (file_exists(BASE_PATH.$url_alias.'/'.$file_ref)) {
                            $file_links[] = $file_ref;
                            $file_links_new[] = DRUPAL_FILE_URL.$url_alias.'/'.$file_ref;
                        } else if (file_exists(BASE_PATH.$current_page_url_alias.'/'.$file_ref)) {
                            $file_links[] = $file_ref;
                            $file_links_new[] = DRUPAL_FILE_URL.$current_page_url_alias.'/'.$file_ref;
                        }
                    }
                }
            }
        } elseif ($tag == 'img') {
            if (strstr($file_ref,'readspeaker_listen_icon.gif')) {
                $file_links_new[] = '';
            } else {
                if (!strstr($file_ref, 'http://') && !strstr($file_ref, 'https://')) {
                    if (file_exists(BASE_PATH.$current_page_url_alias.'/'.$file_ref)) {
                        $file_links[] = $file_ref;
                        $file_links_new[] = DRUPAL_IMG_URL.$current_page_url_alias.'/'.$file_ref;
                    }
                }
            }
        }
    }

    if (count($file_links_new) > 0) {
        $content = str_replace($file_links, $file_links_new, $content);
    }
    return $content;
}
