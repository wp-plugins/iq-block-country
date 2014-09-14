<?php

function iqblockcountry_install_db() {
   global $wpdb;

   $table_name = $wpdb->prefix . "iqblock_logging";
     
   $sql = "CREATE TABLE $table_name (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  datetime datetime NOT NULL,
  ipaddress tinytext NOT NULL,
  country tinytext NOT NULL,
  url varchar(250) DEFAULT '/' NOT NULL,
  banned enum('F','B','A','T') NOT NULL,
  UNIQUE KEY id (id)
);";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
}

function iqblockcountry_uninstall_db() {
   global $wpdb;

   $table_name = $wpdb->prefix . "iqblock_logging";
      
   $sql = "DROP TABLE IF EXISTS `$table_name`;"; 

   $wpdb->query($sql);
   
   delete_option( "blockcountry_dbversion");
}

function iqblockcountry_clean_db()
{
   global $wpdb;

   $table_name = $wpdb->prefix . "iqblock_logging";
   $sql = "DELETE FROM " . $table_name . " WHERE DATE_SUB(CURDATE(),INTERVAL 31 DAY) >= datetime;";
   $wpdb->query($sql);
   
}


function iqblockcountry_update_db_check() {
    if (get_site_option( 'blockcountry_dbversion' ) != DBVERSION) {
        iqblockcountry_install_db();
        update_option( "blockcountry_dbversion", DBVERSION );
    }
}

function iqblockcountry_install_loggingdb() {
   global $wpdb;

   $table_name = $wpdb->prefix . "iqblock_logging_backend";
     
   $sql = "CREATE TABLE $table_name (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  datetime datetime NOT NULL,
  ipaddress tinytext NOT NULL,
  country tinytext NOT NULL,
  url varchar(250) DEFAULT '/' NOT NULL,
  banned enum('NH','WL') NOT NULL,
  UNIQUE KEY id (id)
);";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
}

function iqblockcountry_uninstall_loggingdb() {
   global $wpdb;

   $table_name = $wpdb->prefix . "iqblock_logging_backend";
      
   $sql = "DROP TABLE IF EXISTS `$table_name`;"; 

   $wpdb->query($sql);
   
   delete_option( "blockcountry_dbversion2");
}

function iqblockcountry_clean_loggingdb()
{
   global $wpdb;

   $table_name = $wpdb->prefix . "iqblock_logging_db";
   $sql = "DELETE FROM " . $table_name . " WHERE DATE_SUB(CURDATE(),INTERVAL 14 DAY) >= datetime;";
   $wpdb->query($sql);
}

/*
 * Schedule tracking if this option was set in the admin panel
 */
function iqblockcountry_blockcountry_backendlogging($old_value, $new_value)
{
    if ($old_value !== $new_value)
    {
        if ($new_value == '')
        {
            iqblockcountry_uninstall_loggingdb();
        }
        elseif (!empty($new_value))
        {
            iqblockcountry_install_loggingdb();
        }
    }
}


function iqblockcountry_logging($ipaddress,$country,$banend)
{
    global $wpdb;

    $urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '/' );

    $table_name = $wpdb->prefix . "iqblock_logging";
    $wpdb->insert($table_name,array ('datetime' => current_time('mysql'), 'ipaddress' => $ipaddress, 'country' => $country, 'banned' => $banend,'url' => $urlRequested));
}

function iqblockcountry_logging_backend($ipaddress,$country,$banend)
{
    global $wpdb;

    $urlRequested = (isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '/' );

    $table_name = $wpdb->prefix . "iqblock_logging_backend";
    $wpdb->insert($table_name,array ('datetime' => current_time('mysql'), 'ipaddress' => $ipaddress, 'country' => $country, 'banned' => $banend,'url' => $urlRequested));
}