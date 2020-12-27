# Icon Migrate

## Introduction
Icon Migrate is a collection of PHP scripts that help developers to migrate any site to Drupal 8 and 9.

## Using Icon Migrate

### Taking a static archive

In order to migrate a site, Icon Migrate works with other site archiving tools such as *Site Sucker* and *wget*. An example of using wget is provided below:

```wget --mirror --page-requisites --adjust-extension --no-parent --convert-links --directory-prefix=local_dest_dir https://www.aph.gov.au/About_Parliament/Parliamentary_Departments/Department_of_Parliamentary_Services/Publications/Annual_Report_2016-17```

Those site archiving tools will crawl and make a full static HTML archive of a website with all referenced local files and images. Icon Migrate can then pick up the archived HTML pages, along with the files and images and migrate them into Drupal.

### Preparing the Drupal site

Basic configuration of the Drupal site will be required. An example ```composer.json``` file is provided, which includes the modules those scripts depends on to function properly. In a nutshell, the target Drupal site will need the REST API (create node) enabled.

### Running the Icon Migrate scripts

There are two PHP scripts to run: ```icon_migrate_discover.php``` and ```icon_migrate_post.php```.

These scripts will require the settings files configured properly before doing their jobs.

After the correct settings are provided, run ```php -f icon_migrate_discover.php``` to convert the static HTML archive of the source site into JSON data files that can then be posted to Drupal via Drupal's REST API.

After the JSON data files are prepared, run ```php -dsafe_mode=Off icon_migrate_post.php``` to post the JSON files to Drupal to create the migrated pages  