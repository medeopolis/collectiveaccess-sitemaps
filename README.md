# collectiveaccess-sitemaps

PHP script for generating a sitemap.xml file in Pawtucket's root.

In order to do this it extracts information for Collections, Objects, Places, Occurrences, Entities directly from the SQL database.

1. Choose your settings for generating the sitemap.xml file. Read comments in the file.

2. Execute: php -f sitemap.php

   The script gives you information such as number of URLs created for each kind of item (Collections, Objects, Places, Occurrences, Entities and Total)

   If the "lastmod" date is "1970-01-01CET01:00:00+01:00" for an item, it displays a warning that items ID.

3. Check if sitemap.xml file is created in your Pawtucket's public root and readable by bots.

