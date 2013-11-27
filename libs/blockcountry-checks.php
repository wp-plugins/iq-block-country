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

/*
 *  Check country against bad countries, whitelist and blacklist
 */
function iqblockcountry_check($country,$badcountries,$ip_address)
{
    $blocked = FALSE;
    if (is_array ( $badcountries ) && in_array ( $country, $badcountries )) {
        $blocked = TRUE;
    }
    
    if ((!iqblockcountry_is_login_page() || !is_admin()))
    {    
        $frontendwhitelist = get_option ( 'blockcountry_frontendwhitelist' );
        if (preg_match('/;/',$frontendwhitelist))
        {
            $arr = explode(";", $frontendwhitelist);
            if (is_array ( $arr ) && in_array ( $ip_address, $arr)) {
                $blocked = FALSE;
            }
        }
        $frontendblacklist = get_option ( 'blockcountry_frontendblacklist' );
        if (preg_match('/;/',$frontendblacklist))
        {
            $arr = explode(";", $frontendblacklist);
            if (is_array ( $arr ) && in_array ( $ip_address, $arr)) {
             $blocked = TRUE;
            }
        }
    }
    return $blocked;
}

/*
  * Does the real check of visitor IP against MaxMind database.
 * Looks up country in the Maxmind database and if needed blocks IP.
 */
function iqblockcountry_CheckCountry() {

    $ip_address = "";
    if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip_address = $_SERVER["REMOTE_ADDR"];
    } else {
        $ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
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
                // Display block message
		print "$blockmessage";
                
                if ((iqblockcountry_is_login_page() || is_admin()) && get_option('blockcountry_blockbackend'))
                {
                    $blocked = get_option('blockcountry_backendnrblocks');
                    if (empty($blocked)) { $blocked = 0; }
                    $blocked++;
                    update_option('blockcountry_backendnrblocks', $blocked);
                }
                else
                {
                    $blocked = get_option('blockcountry_frontendnrblocks');
                    if (empty($blocked)) { $blocked = 0; }
                    $blocked++;
                    update_option('blockcountry_frontendnrblocks', $blocked);
                }

		exit ();
	}
		
    }
	
}


/*
 * Check if page is the login page
 */
function iqblockcountry_is_login_page() {
    return !strncmp($_SERVER['REQUEST_URI'], '/wp-login.php', strlen('/wp-login.php'));
}

/*
 * Check if Geo databases needs to be updated.
 */
function iqblockcountry_checkupdatedb()
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
}
