<?php


function iqblockcountry_check_ipaddress($ip_address)
{
    if (!class_exists('GeoIP'))
    {
	include_once("geoip.inc");
    }
    
    if ((file_exists ( IPV4DBFILE )) && function_exists('geoip_open')) {

	$ipv4 = FALSE;
	$ipv6 = FALSE;
	if (iqblockcountry_is_valid_ipv4($ip_address)) { $ipv4 = TRUE; }
	if (iqblockcountry_is_valid_ipv6($ip_address)) { $ipv6 = TRUE; }
	
	if ($ipv4) 
	{ 	
		$gi = geoip_open ( IPV4DBFILE, GEOIP_STANDARD );
		$country = geoip_country_code_by_addr ( $gi, $ip_address );
		geoip_close ( $gi );
	}
	elseif ($ipv6)
	{
		if (file_exists ( IPV6DBFILE )) {				
			$gi = geoip_open(IPV6DBFILE,GEOIP_STANDARD);
			$country = geoip_country_code_by_addr_v6 ( $gi, $ip_address );
 			geoip_close($gi);
		}
		else {
			$country = 'ipv6';				
		}
	}
        else { $country = "Unknown"; }
        }
        else { $country = "Unknown"; }
        
    return $country;
}


function iqblockcountry_check_city_ipaddress($ip_address)
{
    if (!class_exists('GeoIP'))
    {
	include_once("geoip.inc");
    }
    include_once("geoipcity.inc");
    include_once("geoipregionvars.php");

    if ((file_exists ( IPV4DBFILE )) && function_exists('geoip_open')) {

	$ipv4 = FALSE;
	$ipv6 = FALSE;
	if (iqblockcountry_is_valid_ipv4($ip_address)) { $ipv4 = TRUE; }
	if (iqblockcountry_is_valid_ipv6($ip_address)) { $ipv6 = TRUE; }
	
	if ($ipv4) 
	{ 	
		$gi = geoip_open ( CITY4DBFILE, GEOIP_STANDARD );
		$record = geoip_record_by_addr ( $gi, $ip_address );
		geoip_close ( $gi );
	}
	elseif ($ipv6)
	{
		if (file_exists ( CITY6DBFILE )) {				
			$gi = geoip_open(CITY6DBFILE,GEOIP_STANDARD);
                        $record = GeoIP_record_by_addr_v6 ( $gi, $ip_address );
			geoip_close($gi);
		}
                else 
                { $record = "Unknown";
                }
	}
        else { $record = "Unknown"; }
        }
        else { $record = "Unknown"; }
        
    return $record;
}


/*
 *  Check country against bad countries, whitelist and blacklist
 */
function iqblockcountry_check($country,$badcountries,$ip_address)
{
    /* Set default blocked status and get all options */
    $blocked = FALSE; 
    $blockedpage = get_option('blockcountry_blockpages');
    $blockedcategory = get_option('blockcountry_blockcategories');

    $frontendblacklistip = array();   $frontendblacklist = get_option ( 'blockcountry_frontendblacklist' );
    $frontendwhitelistip = array();   $frontendwhitelist = get_option ( 'blockcountry_frontendwhitelist' );
    $backendblacklistip = array();    $backendblacklist = get_option ( 'blockcountry_backendblacklist' );
    $backendwhitelistip = array();    $backendwhitelist = get_option ( 'blockcountry_backendwhitelist' );
    
    $backendbanlistip = unserialize(get_option('blockcountry_backendbanlistip'));
    $blockredirect = get_option ( 'blockcountry_redirect');
    
   
    if (preg_match('/;/',$frontendblacklist))
    {
        $frontendblacklistip = explode(";", $frontendblacklist);
    }
    if (preg_match('/;/',$frontendwhitelist))
    {
            $frontendwhitelistip = explode(";", $frontendwhitelist);
    }
    if (preg_match('/;/',$backendblacklist))
    {
        $backendblacklistip = explode(";", $backendblacklist);
    }
    if (preg_match('/;/',$backendwhitelist))
    {
            $backendwhitelistip = explode(";", $backendwhitelist);
    }
    
    /* Block if user is in a bad country from frontend or backend. Unblock may happen later */
    if (is_array ( $badcountries ) && in_array ( $country, $badcountries )) {
        $blocked = TRUE;
        global $backendblacklistcheck;
        $backendblacklistcheck = TRUE;
    }

    /* Check if requested url is not login page. Else check against frontend whitelist/blacklist. */
    if (!iqblockcountry_is_login_page() )
    {    
        if (is_array ( $frontendblacklistip ) && in_array ( $ip_address, $frontendblacklistip)) {
             $blocked = TRUE;
            }
        if (is_array ( $frontendwhitelistip ) && in_array ( $ip_address, $frontendwhitelistip)) {
                $blocked = FALSE;
            }
    }
    
    
    if (iqblockcountry_is_login_page() )
    {    
        if (is_array($backendbanlistip) &&  in_array($ip_address,$backendbanlistip))
        {
            $blocked = TRUE;
            global $apiblacklist;
            $apiblacklist = TRUE;
        }
        if (is_array ( $backendblacklistip ) && in_array ( $ip_address, $backendblacklistip)) {
             $blocked = TRUE;
            }
        if (is_array ( $backendwhitelistip ) && in_array ( $ip_address, $backendwhitelistip)) {
                $blocked = FALSE;
            }
    }
    
    if (is_page() && $blockedpage == "on")
    {
        $blockedpages = get_option('blockcountry_pages');
        $frontendblacklist = get_option ( 'blockcountry_frontendblacklist' );
        if (is_page($blockedpages) && !empty($blockedpages) && ((is_array ( $badcountries ) && in_array ( $country, $badcountries ) || (is_array ( $frontendblacklistip ) && in_array ( $ip_address, $frontendblacklistip)))))
        {
            $blocked = TRUE;
            if (is_array ( $frontendwhitelistip ) && in_array ( $ip_address, $frontendwhitelistip)) {
                $blocked = FALSE;
            }
        }
        else
        {
            $blocked = FALSE;
        }
    }
    if (is_single() && $blockedcategory == "on")
    {
        global $post;
        $blockedcategories = get_option('blockcountry_categories');
        if (!is_array($blockedcategories)) { $blockedcategories = array(); }
        $post_categories = wp_get_post_categories( $post->ID );
        $flagged = FALSE;
        foreach ($post_categories as $key => $value)
        {
            if (in_array($value,$blockedcategories))
            {
                if (is_single() && ((is_array ( $badcountries ) && in_array ( $country, $badcountries ) || (is_array ( $frontendblacklistip ) && in_array ( $ip_address, $frontendblacklistip)))))
                {
                    $flagged = TRUE;
                    if (is_array ( $frontendwhitelistip ) && in_array ( $ip_address, $frontendwhitelistip)) {
                        $flagged = FALSE;
                    }
                }
            }            
        }
        if ($flagged) { $blocked = TRUE; } else { $blocked = FALSE; }
    }
    if (is_category() && $blockedcategory == "on")
    {
        $blockedcategories = get_option('blockcountry_categories');
        if (!is_array($blockedcategories)) { $blockedcategories = array(); }
        if (is_category($blockedcategories))
        {
            $blocked = TRUE;
        }
        else
        {
            $blocked = FALSE;
        }
    }
    
    
    if (is_home() && (get_option('blockcountry_blockhome')) == FALSE && $blockedcategory == "on")
    {
        $blocked = FALSE;
    }
    if (is_page($blockredirect) && ($blockredirect != 0) && !(empty($blockredirect)))
    {
        $blocked = FALSE;
    }
    
    $allowse = get_option('blockcountry_allowse');
    if (!iqblockcountry_is_login_page() && isset ($_SERVER['HTTP_USER_AGENT']) && iqblockcountry_check_searchengine($_SERVER['HTTP_USER_AGENT'], $allowse))
    {
        $blocked = FALSE;
    }
    
    return $blocked;
}

/*
  * Does the real check of visitor IP against MaxMind database.
 * Looks up country in the Maxmind database and if needed blocks IP.
 */
function iqblockcountry_CheckCountry() {

    $ip_address = "";
    
    if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
    $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $ip_address = trim($ips[0]);
    } elseif ( isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ) {
    $ip_address = $_SERVER['HTTP_X_REAL_IP'];
    } elseif ( isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) ) {
    $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( isset($_SERVER['HTTP_X_TM_REMOTE_ADDR']) && !empty($_SERVER['HTTP_X_TM_REMOTE_ADDR']) ) {
    $ip_address = $_SERVER['HTTP_X_TM_REMOTE_ADDR'];
    }
    
    
        
    $ip_address = iqblockcountry_get_ipaddress();
    $country = iqblockcountry_check_ipaddress($ip_address);

    if ((iqblockcountry_is_login_page() || is_admin()) && get_option('blockcountry_blockbackend'))
    { 
        $badcountries = get_option( 'blockcountry_backendbanlist' );
    }
    else
    {    
        $badcountries = get_option( 'blockcountry_banlist' );
    }
    $blocklogin = get_option ( 'blockcountry_blocklogin' );
    if ( ((is_user_logged_in()) && ($blocklogin != "on")) || (!(is_user_logged_in())) )  {			
	
        /* Check ip address against banlist, whitelist and blacklist */
        if (iqblockcountry_check($country,$badcountries,$ip_address))
        {        
        	$blockmessage = get_option ( 'blockcountry_blockmessage' );
                $blockredirect = get_option ( 'blockcountry_redirect');
                $header = get_option('blockcountry_header');
                if (!empty($header) && ($header))
                {
                    // Prevent as much as possible that this error message is cached:
                    header("Cache-Control: no-store, no-cache, must-revalidate");
                    header("Cache-Control: post-check=0, pre-check=0", false);
                    header("Pragma: no-cache");
                    header("Expires: Sat, 26 Jul 2012 05:00:00 GMT"); 
                                
                    header ( 'HTTP/1.1 403 Forbidden' );
                }
                if (!empty($blockredirect) && $blockredirect != 0)
                {
                    $redirecturl = get_permalink($blockredirect);
                    header("Location: $redirecturl");
                }
                // Display block message
                print "$blockmessage";

                if ((iqblockcountry_is_login_page() || is_admin()) && get_option('blockcountry_blockbackend'))
                {
                    $blocked = get_option('blockcountry_backendnrblocks');
                    if (empty($blocked)) { $blocked = 0; }
                    $blocked++;
                    update_option('blockcountry_backendnrblocks', $blocked);
                    global $apiblacklist,$backendblacklistcheck;
                    if (!$apiblacklist)
                    {    
                        iqblockcountry_logging($ip_address, $country, "B");
                    }
                    elseif ($backendblacklistcheck && $apiblacklist)
                    {
                        iqblockcountry_logging($ip_address, $country, "T");
                    }
                    else
                    {
                        iqblockcountry_logging($ip_address, $country, "A");                        
                    }
                }
                else
                {
                    $blocked = get_option('blockcountry_frontendnrblocks');
                    if (empty($blocked)) { $blocked = 0; }
                    $blocked++;
                    update_option('blockcountry_frontendnrblocks', $blocked);
                    iqblockcountry_logging($ip_address, $country, "F");
                }

		exit ();
	}		
    }
}


/*
 * Check if page is the login page
 */
function iqblockcountry_is_login_page() {
    $found = FALSE;
    $pos = strpos( $_SERVER['REQUEST_URI'], 'wp-login' );
    if ($pos !== false)
    { return TRUE; }
    else { return FALSE; }
}

/*
 * Check if Geo databases needs to be updated.
 */
function iqblockcountry_checkupdatedb()
{
    if (get_option('blockcountry_automaticupdate') == 'on')
    {
        $lastupdate = get_option('blockcountry_lastupdate');
        if (empty($lastupdate)) { $lastupdate = 0; }
        $time = $lastupdate + 86400 * 31;
  
        if(time() > $time)
        {
            iqblockcountry_downloadgeodatabase("4", false);
            iqblockcountry_downloadgeodatabase("6", false);
            update_option('blockcountry_lastupdate' , time());
        }
    
        if (! (file_exists ( IPV4DBFILE )))     {   iqblockcountry_downloadgeodatabase("4", false);   }
        if (! (file_exists ( IPV6DBFILE )))     {   iqblockcountry_downloadgeodatabase("6", false);   }
    
    }
}
