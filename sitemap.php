<?php
// Code by Clovis Darrigan - https://darrigan.net
// Date: 15/07/2022
// Version: 1.0.2
// PHP script generating a sitemap.xml file in Pawtucket's root.
// Extract informations directly from SQL database for Collections, Objects, Places, Occurrences, Entities.

/* Further changes by Medeopolis (C) 2024 */

// Use CA settings directly
require __DIR__ . '/setup.php';

// *** Settings for sitemap ***

// URL root for your public website (Pawtucket root) including index.php
$URL_root = __CA_URL_ROOT__ ;

// TODO: Move settings to __CA_CONF_DIR__.'/sitemap.conf
// requires a configuration parser here unless we import CA's

// Settings for items:
// - Changefreq_* is a string in {always;hourly;daily;weekly;monthly;yearly;never} (Google won't consider it)
// - Priority_* is a real number between 0.0 and 1.0 (Google won't consider it)
// - Skip_* is a list of items which will be ignored (not included in sitemap.xml). Enter unique IDs like: "'30','32'". Default must be "'0'".

// Collections
$Priority_Collections = "0.8" ;
$Changefreq_Collections = "yearly" ;
$Skip_Collections = "'0'";

// Objects
$Priority_Objects = "0.7" ;
$Changefreq_Objects = "weekly" ;
$Skip_Objects = "'0'";

// Places
$Priority_Places = "0.5" ;
$Changefreq_Places = "monthly" ;
$Skip_Places = "'0'";

// Occurrences
$Priority_Occurrences = "0.5" ;
$Changefreq_Occurrences = "monthly" ;
$Skip_Occurrences = "'0'";

// Entities
$Priority_Entities = "0.5" ;
$Changefreq_Entities = "monthly" ;
$Skip_Entities = "'0'";
// *** End of settings ***


// *** Connection to SQL database ***
$connection = new mysqli(__CA_DB_HOST__,__CA_DB_USER__,__CA_DB_PASSWORD__) or die("Connection error.");
$connection->select_db(__CA_DB_DATABASE__);
if ($connection->connect_error) {
	die('Connection error: ' . $connection->connect_error);
}

// *** Generating xml file ***
// File header
$xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

// Special pages (added by hand)

// Index page
$xml .= "<url>\n";
$xml .= "	<loc>$URL_root</loc>\n";
//$xml .= "	<lastmod></lastmod>\n";
$xml .= "	<changefreq>weekly</changefreq>\n";
$xml .= "	<priority>0.9</priority>\n";
$xml .= "</url>\n";

// About page
$xml .= "<url>\n";
$xml .= "	<loc>$URL_root/About/Index</loc>\n";
//$xml .= "	<lastmod></lastmod>\n";
$xml .= "	<changefreq>monthly</changefreq>\n";
$xml .= "	<priority>0.4</priority>\n";
$xml .= "</url>\n";

// Gallery page
$xml .= "<url>\n";
$xml .= "	<loc>$URL_root/Gallery/Index</loc>\n";
//$xml .= "	<lastmod></lastmod>\n";
$xml .= "	<changefreq>monthly</changefreq>\n";
$xml .= "	<priority>0.2</priority>\n";
$xml .= "</url>\n";

// Collections page
$xml .= "<url>\n";
$xml .= "	<loc>$URL_root/Collections/index</loc>\n";
//$xml .= "	<lastmod></lastmod>\n";
$xml .= "	<changefreq>monthly</changefreq>\n";
$xml .= "	<priority>0.8</priority>\n";
$xml .= "</url>\n";

// Counts special pages
$count = 4;

// Get others items automatically (only public and undeleted)

// Collections
$table_num = 13; // Don't touch this!
$req = "SELECT collection_id FROM ca_collections WHERE deleted = 0 AND access = 1 AND collection_id NOT IN ($Skip_Collections)";
//echo "\n\n$req\n\n";
$res = $connection->query($req) or die("Error on request #1.");
$num = $res->num_rows;
$i = 0;
if ($num<1)
{
// Skip
}
else
{
	while($line = $res->fetch_assoc()) 
	{
		$id = $line['collection_id'];
		// Get the last modified date
		$req2 = "SELECT log_datetime FROM ca_change_log WHERE logged_table_num = $table_num AND logged_row_id = $id AND changetype = 'U' ORDER BY log_datetime DESC LIMIT 1";
		//echo "\n$req2\n";
		$res2 = $connection->query($req2) or die("Error on request #2.");
		$line2 = $res2->fetch_assoc();
		$lastmod = $line2['log_datetime'];
		$res2->close();
		$lastmod = date('Y-m-dTH:i:sP', $lastmod);
		if ($lastmod == "1970-01-01CET01:00:00+01:00") echo "*** Verify $id\n";
		$xml .= "<url>\n";
		$xml .= "	<loc>$URL_root/Detail/collections/$id</loc>\n";
		$xml .= "	<lastmod>$lastmod</lastmod>\n";
		$xml .= "	<changefreq>$Changefreq_Collections</changefreq>\n";
		$xml .= "	<priority>$Priority_Collections</priority>\n";
		$xml .= "</url>\n";
		$count++;
		$i++;
	}
}
$res->close();
echo "Collections URLs: $i\n";


// Objects
$table_num = 57; // Don't touch this!
$req = "SELECT object_id FROM ca_objects WHERE deleted = 0 AND access = 1 AND object_id NOT IN ($Skip_Objects)";
//echo "\n\n$req\n\n";
$res = $connection->query($req) or die("Error on request #1.");
$num = $res->num_rows;
$i = 0;
if ($num<1)
{
// Skip
}
else
{
	while($line = $res->fetch_assoc()) 
	{
		$id = $line['object_id'];
		// Get the last modified date
		$req2 = "SELECT log_datetime FROM ca_change_log WHERE logged_table_num = $table_num AND logged_row_id = $id AND changetype = 'U' ORDER BY log_datetime DESC LIMIT 1";
		//echo "\n$req2\n";
		$res2 = $connection->query($req2) or die("Error on request #2.");
		$line2 = $res2->fetch_assoc();
		$lastmod = $line2['log_datetime'];
		$res2->close();
		$lastmod = date('Y-m-dTH:i:sP', $lastmod);
		if ($lastmod == "1970-01-01CET01:00:00+01:00") echo "*** Verify $id\n";
		$xml .= "<url>\n";
		$xml .= "	<loc>$URL_root/Detail/objects/$id</loc>\n";
		$xml .= "	<lastmod>$lastmod</lastmod>\n";
		$xml .= "	<changefreq>$Changefreq_Objects</changefreq>\n";
		$xml .= "	<priority>$Priority_Objects</priority>\n";
		$xml .= "</url>\n";
		$count++;
		$i++;
	}
}
$res->close();
echo "Objects URLs: $i\n";


// Places
$table_num = 72; // Don't touch this!
$req = "SELECT place_id FROM ca_places WHERE deleted = 0 AND access = 1 AND place_id NOT IN ($Skip_Places)";
//echo "\n\n$req\n\n";
$res = $connection->query($req) or die("Error on request #1.");
$num = $res->num_rows;
$i = 0;
if ($num<1)
{
// Skip
}
else
{
	while($line = $res->fetch_assoc()) 
	{
		$id = $line['place_id'];
		// Get the last modified date
		$req2 = "SELECT log_datetime FROM ca_change_log WHERE logged_table_num = $table_num AND logged_row_id = $id AND changetype = 'U' ORDER BY log_datetime DESC LIMIT 1";
		//echo "\n$req2\n";
		$res2 = $connection->query($req2) or die("Error on request #2.");
		$line2 = $res2->fetch_assoc();
		$lastmod = $line2['log_datetime'];
		$res2->close();
		$lastmod = date('Y-m-dTH:i:sP', $lastmod);
		if ($lastmod == "1970-01-01CET01:00:00+01:00") echo "*** Verify $id\n";
		$xml .= "<url>\n";
		$xml .= "	<loc>$URL_root/Detail/places/$id</loc>\n";
		$xml .= "	<lastmod>$lastmod</lastmod>\n";
		$xml .= "	<changefreq>$Changefreq_Places</changefreq>\n";
		$xml .= "	<priority>$Priority_Places</priority>\n";
		$xml .= "</url>\n";
		$count++;
		$i++;
	}
}
$res->close();
echo "Places URLs: $i\n";


// Occurrences
$table_num = 67; // Don't touch this!
$req = "SELECT occurrence_id FROM ca_occurrences WHERE deleted = 0 AND access = 1 AND occurrence_id NOT IN ($Skip_Occurrences)";
//echo "\n\n$req\n\n";
$res = $connection->query($req) or die("Error on request #1.");
$num = $res->num_rows;
$i = 0;
if ($num<1)
{
// Skip
}
else
{
	while($line = $res->fetch_assoc()) 
	{
		$id = $line['occurrence_id'];
		// Get the last modified date
		$req2 = "SELECT log_datetime FROM ca_change_log WHERE logged_table_num = $table_num AND logged_row_id = $id AND changetype = 'U' ORDER BY log_datetime DESC LIMIT 1";
		//echo "\n$req2\n";
		$res2 = $connection->query($req2) or die("Error on request #2.");
		$line2 = $res2->fetch_assoc();
		$lastmod = $line2['log_datetime'];
		$res2->close();
		$lastmod = date('Y-m-dTH:i:sP', $lastmod);
		if ($lastmod == "1970-01-01CET01:00:00+01:00") echo "*** Verify $id\n";
		$xml .= "<url>\n";
		$xml .= "	<loc>$URL_root/Detail/occurrences/$id</loc>\n";
		$xml .= "	<lastmod>$lastmod</lastmod>\n";
		$xml .= "	<changefreq>$Changefreq_Occurrences</changefreq>\n";
		$xml .= "	<priority>$Priority_Occurrences</priority>\n";
		$xml .= "</url>\n";
		$count++;
		$i++;
	}
}
$res->close();
echo "Occurrences URLs: $i\n";


// Entities
$table_num = 20; // Don't touch this!
$req = "SELECT entity_id FROM ca_entities WHERE deleted = 0 AND access = 1 AND entity_id NOT IN ($Skip_Entities)";
//echo "\n\n$req\n\n";
$res = $connection->query($req) or die("Error on request #1.");
$num = $res->num_rows;
$i = 0;
if ($num<1)
{
// Skip
}
else
{
	while($line = $res->fetch_assoc()) 
	{
		$id = $line['entity_id'];
		// Get the last modified date
		$req2 = "SELECT log_datetime FROM ca_change_log WHERE logged_table_num = $table_num AND logged_row_id = $id AND changetype = 'U' ORDER BY log_datetime DESC LIMIT 1";
		//echo "\n$req2\n";
		$res2 = $connection->query($req2) or die("Error on request #2.");
		$line2 = $res2->fetch_assoc();
		$lastmod = $line2['log_datetime'];
		$res2->close();
		$lastmod = date('Y-m-dTH:i:sP', $lastmod);
		if ($lastmod == "1970-01-01CET01:00:00+01:00") echo "*** Verify $id\n";
		$xml .= "<url>\n";
		$xml .= "	<loc>$URL_root/Detail/entities/$id</loc>\n";
		$xml .= "	<lastmod>$lastmod</lastmod>\n";
		$xml .= "	<changefreq>$Changefreq_Entities</changefreq>\n";
		$xml .= "	<priority>$Priority_Entities</priority>\n";
		$xml .= "</url>\n";
		$count++;
		$i++;
	}
}
$res->close();
echo "Entities URLs: $i\n";



// File footer
$xml .=  "</urlset>";

// *** End of generating xml file ***

// *** Write xml file ***
$file = fopen(__CA_BASE_DIR__."sitemap.xml", "w");
fwrite($file,$xml);
fclose($file);

echo "\nTotal URLs in sitemap: $count\n";
if ($count > 50000) echo "\nTotal number of URLs should not be > 50000.\n";
echo "\nDate: ".date('Y-m-dTH:i:sP')."\n";

// *** End ***
?>
