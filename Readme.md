# Icon Migrate

## Introduction
Icon Migrate is a collection of PHP scripts that help developers to migrate any site to Drupal 8 and 9.

## Using Icon Migrate

### Taking a static archive

In order to migrate a site, Icon Migrate works with other site archiving tools such as *Site Sucker* and *wget*. An example of using wget is provided below:

```wget --mirror --page-requisites --adjust-extension --no-parent --convert-links --directory-prefix=local_dest_dir https://example.com```

Those site archiving tools will crawl and make a full static HTML archive of a website with all referenced local files and images. Icon Migrate can then pick up the archived HTML pages, along with the files and images and migrate them into Drupal.

### Preparing the Drupal site

Basic configuration of the Drupal site will be required. An example ```composer.json``` file is provided, which includes the modules those scripts depends on to function properly. In a nutshell, the target Drupal site will need the REST API (create node) enabled.

It is also useful to enable the Devel module as well as the Devel Generate module. This will allow quick delete of all imported nodes for debug purposes

`` drush genc --kill 0 0 ``
NOTE: If drush command is terminating abnormally and unable to delete the nodes, please drop the database and import again.

### Test the API
The following curl command can be used to test the API configuration by creating one node.
```
curl --insecure \
  --include \
  --request POST \
  --user 'admin:1Drupal!@#' \
  --header 'Content-type: application/hal+json' \
  http://govcms-ditrdc.local:88/node?_format=hal_json \
  --data-binary '{"_links":{"type":{"href":"http://govcms-ditrdc.local:88/rest/type/node/simple_content"}},"title":[{"value":"This is a test"}],"type":[{"target_id":"simple_content"}],"path":[{"alias":"/restful-api-test"}],"body":[{"value":"<p>This is a test</p>","format":"full_html"}],"field_topics":[{"target_id":"10403","target_type":"taxonomy_term"}]}'
```

### Running the Icon Migrate scripts

There are two PHP scripts to run: ```icon_migrate_discover.php``` and ```icon_migrate_post.php```.

These scripts will require the settings files configured properly before doing their jobs.

After the correct settings are provided, run ```php -f icon_migrate_discover.php``` to convert the static HTML archive of the source site into JSON data files that can then be posted to Drupal via Drupal's REST API.

After the JSON data files are prepared, run ```php -dsafe_mode=Off icon_migrate_post.php``` to post the JSON files to Drupal to create the migrated pages  


### Rewrite node paths as needed
For custom node paths, update the array $nodes_paths_custom_**** array in settings.discover.php. Set the correct arry in get_new_path_for_node() run the script.
Drupal nodes will be created as per the cutom path rules set in $nodes_paths_custom_**** array.


### Setup 301 Redirects
Redirects should be setup in Drupal so that Old Urls are mapped to New URL's
As the script runs, it will create a CSV file in the CSV directory. 
NOTE : Please clear the CSV file before running the script.
Generated CSV file can be imported easily using https://www.drupal.org/project/path_redirect_import module.
adjustments should be made in append_to_csv_file_for_redirects().


#TODO
- remove <div id="text"> and it closing </div>, e.g. https://ditrdc-dev.iconinc.com.au/node/23800/edit
- in <a> tags where it links to a relative path within the site, change the link inside the *href* attribute to match the page in the new IA
- update the CSV so it does not include the source directory such as 'regional-gov-au'