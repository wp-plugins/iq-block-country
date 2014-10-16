<?php

/*
 * Download the GeoIP database from MaxMind
 */
function iqblockcountry_downloadgeodatabase($version, $displayerrors) 
{
 if ($version == 4)
 {
     iqblockcountry_download_geodb(IPV4DB, IPV4DBFILE, $displayerrors);
 }
 elseif ($version == 6)
 {
     iqblockcountry_download_geodb(IPV6DB, IPV6DBFILE, $displayerrors);
 }    
}

/*
 * Download a Geo DB file
 */

function iqblockcountry_download_geodb($url,$geofile,$displayerror)
{
 if( !class_exists( 'WP_Http' ) )
        include_once( ABSPATH . WPINC. '/class-http.php' );

 $args = array(
    'timeout'     => 15);
 $request = new WP_Http ();
 $result = $request->request ( $url ,$args);
 $content = array ();

 if (is_array($result) && array_key_exists('response',$result) && (in_array ( '403', $result ['response'] )) && (preg_match('/Rate limited exceeded, please try again in 24 hours./', $result['body'] )) )  
 {
    if($displayerror)
    {
        ?>
 	<p><?php _e('Error occured: Could not download the GeoIP database from'); ?> <?php echo " " . $url;?><br />
	<?php _e('MaxMind has blocked requests from your IP address for 24 hours. Please check again in 24 hours or download this file from your own PC'); ?><br />
	<?php _e('Unzip this file and upload it (via FTP for instance) to:'); ?><strong> <?php echo $geofile;?></strong></p>
        <?php
    }
 }
 elseif ((isset ( $result->errors )) || (! (in_array ( '200', $result ['response'] )))) 
 {
    if($displayerror){
        print_r($result);
        ?>
 	<p><?php _e('Error occured: Could not download the GeoIP database from'); ?> <?php echo " " . $url;?><br />
	<?php _e('Please download this file from your own PC unzip this file and upload it (via FTP for instance) to:'); ?><strong> <?php echo $geofile;?></strong></p>
        <?php
    }
 } 
 else 
 {
	/* Download file */
	if (file_exists ( $geofile . ".gz" )) { unlink ( $geofile . ".gz" ); }
	$content = $result ['body'];
	$fp = fopen ( $geofile . ".gz", "w" );
	fwrite ( $fp, "$content" );
	fclose ( $fp );
		
	/* Unzip this file and throw it away afterwards*/
	$zd = gzopen ( $geofile . ".gz", "r" );
	$buffer = gzread ( $zd, 2000000 );
	gzclose ( $zd );
	if (file_exists ( $geofile . ".gz" )) { unlink ( $geofile . ".gz" ); }
			
	/* Write this file to the GeoIP database file */
	if (file_exists ( $geofile )) { unlink ( $geofile ); } 
	$fp = fopen ( $geofile, "w" );
	fwrite ( $fp, "$buffer" );
	fclose ( $fp );
        update_option('blockcountry_lastupdate' , time());
        if($displayerror)
        {
	   print "<p>" . _e('Finished downloading', 'iqblockcountry') . "</p>";
        }
 }
 if (! (file_exists ( IPV4DBFILE ))) 
 {
    if($displayerror)
    {
	?> 
	<p><?php echo __('Fatal error: GeoIP') . " " . IPV4DBFILE . " " . __('database does not exists. This plugin will not work until the database file is present.'); ?></p>
	<?php
    }
 }
    
}

?>