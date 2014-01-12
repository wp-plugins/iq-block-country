<?php

/*
 * Create the wp-admin menu for iQ Block Country
 */
function iqblockcountry_create_menu() 
{
	//create new menu option in the settings department
	add_submenu_page ( 'options-general.php', 'iQ Block Country', 'iQ Block Country', 'administrator', __FILE__, 'iqblockcountry_settings_page' );
	//call register settings function
	add_action ( 'admin_init', 'iqblockcountry_register_mysettings' );
}

/*
 * Register all settings.
 */
function iqblockcountry_register_mysettings() 
{
	//register our settings
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_banlist' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_backendbanlist' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_backendblacklist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_backendwhitelist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_frontendblacklist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_frontendwhitelist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blockmessage' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blocklogin' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blockfrontend' );
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blockbackend' );
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_header');
        register_setting ( 'iqblockcountry-settings-group2', 'blockcountry_blockpages');
        register_setting ( 'iqblockcountry-settings-group2', 'blockcountry_pages');
}

/*
 * Set default values when activating this plugin.
 */
function iqblockcountry_set_defaults() 
{
    
        update_option('blockcountry_version',VERSION);
        update_option('blockcountry_lastupdate' , 0);
        update_option('blockcountry_blockfrontend' , 'on');
	update_option('blockcountry_backendnrblocks', 0);
	update_option('blockcountry_frontendnrblocks', 0);
	update_option('blockcountry_header', 'on');
        iqblockcountry_install_db();
        
}


function iqblockcountry_uninstall() //deletes all the database entries that the plugin has created
{
        iqblockcountry_uninstall_db();
    	delete_option('blockcountry_banlist' );
	delete_option('blockcountry_backendbanlist' );
	delete_option('blockcountry_backendblacklist' );
	delete_option('blockcountry_backendwhitelist' );
	delete_option('blockcountry_frontendblacklist' );
	delete_option('blockcountry_frontendwhitelist' );
	delete_option('blockcountry_blockmessage' );
	delete_option('blockcountry_backendnrblocks' );
	delete_option('blockcountry_frontendnrblocks' );
	delete_option('blockcountry_blocklogin' );
	delete_option('blockcountry_blockfrontend' );
	delete_option('blockcountry_blockbackend' );
        delete_option('blockcountry_lastupdate');
        delete_option('blockcountry_version');
        delete_option('blockcountry_header');
        delete_option('blockcountry_blockpages');        
        delete_option('blockcountry_pages');        
}



function iqblockcountry_settings_tools() {
    ?>
        <h3><?php _e('Check which country belongs to an IP Address according to the current database.', 'iqblockcountry'); ?></h3>
   
	<form name="ipcheck" action="#ipcheck" method="post">
        <input type="hidden" name="action" value="ipcheck" />
        <?php _e('IP Address to check:', 'iqblockcountry'); ?> <input type="text" name="ipaddress" lenth="50" />
<?php 
        if ( isset($_POST['action']) && $_POST[ 'action' ] == 'ipcheck') {
                    if (isset($_POST['ipaddress']) && !empty($_POST['ipaddress']))
                    {
                        $ip_address = $_POST['ipaddress'];
                        $country = iqblockcountry_check_ipaddress($ip_address);
                        $countrylist = iqblockcountry_get_countries();
                        if ($country == "Unknown" || $country == "ipv6" || $country == "")
                        {
                            echo "<p>" . __('No country for', 'iqblockcountry') . ' ' . $ip_address . ' ' . __('could be found. Or', 'iqblockcountry') . ' ' . $ip_address . ' ' . __('is not a valid IPv4 or IPv6 IP address', 'iqblockcountry'); 
                            echo "</p>";
                        }
                        else {
                            $displaycountry = $countrylist[$country];
                            echo "<p>" . __('IP Adress', 'iqblockcountry') . ' ' . $ip_address . ' ' . __('belongs to', 'iqblockcountry') . ' ' . $displaycountry . ".</p>";
                            $haystack = get_option('blockcountry_banlist');
                            if (is_array($haystack) && in_array ( $country, $haystack )) {
				_e('This country is not permitted to visit the frontend of this website.', 'iqblockcountry');
                                echo "<br />";
                            }
                            $haystack = get_option('blockcountry_backendbanlist');
                            if (is_array($haystack) && in_array ( $country, $haystack )) {
				_e('This country is not permitted to visit the backend of this website.', 'iqblockcountry');
                                echo "<br />";
                            }
                        }
                    }    
		}
        echo '<div class="submit"><input type="submit" name="test" value="' . __( 'Check IP address', 'iqblockcountry' ) . '" /></div>';
        wp_nonce_field('iqblockcountry');
?>		
        </form>
        
        <hr />
        <h3>Download GeoIP database</h3>
        <?php
        $dateformat = get_option('date_format');
        $time = get_option('blockcountry_lastupdate');
        
        $lastupdated = date($dateformat,$time);
        
	echo "<strong>"; _e('The GeoIP database is updated once a month. Last update: ', 'iqblockcountry'); echo $lastupdated; echo ".</strong>.<br />"; 
            _e('If you need a manual update please press buttons below to update.', 'iqblockcountry');
        ?>
        
	<form name="download_geoip" action="#download" method="post">
        <input type="hidden" name="action" value="download" />
<?php 
        echo '<div class="submit"><input type="submit" name="test" value="' . __( 'Download new GeoIP IPv4 Database', 'iqblockcountry' ) . '" /></div>';
        wp_nonce_field('iqblockcountry');
        echo '</form>';
?>		
		<form name="download_geoip6" action="#download6" method="post">
        <input type="hidden" name="action" value="download6" />
<?php 
        echo '<div class="submit"><input type="submit" name="test" value="' . __( 'Download new GeoIP IPv6 Database', 'iqblockcountry' ) . '" /></div>';
        wp_nonce_field('iqblockcountry');
        echo '</form>';
        
        if ( isset($_POST['action']) && $_POST[ 'action' ] == 'download') {
			_e ( 'Downloading...' );	
			iqblockcountry_downloadgeodatabase('4', true);	
		}
        if ( isset($_POST['action']) && $_POST[ 'action' ] == 'download6') {
			_e ( 'Downloading...' );	
			iqblockcountry_downloadgeodatabase('6', true);	
		}
    
}

function iqblockcountry_settings_pages() {
    ?>
    <h3><?php _e('Select which pages are blocked.', 'iqblockcountry'); ?></h3>
    <form method="post" action="options.php">
<?php
    settings_fields ( 'iqblockcountry-settings-group2' );
?>
    <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    
    <tr valign="top">
        <th width="30%"><?php _e('Do you want to block individual pages:', 'iqblockcountry'); ?><br />
        <?php _e('If you do not select this option all pages will be blocked.', 'iqblockcountry'); ?></th>
    <td width="70%">
	<input type="checkbox" name="blockcountry_blockpages" value="on" <?php checked('on', get_option('blockcountry_blockpages'), true); ?> /> 	
    </td></tr>
    <tr valign="top">
    <th width="30%"><?php _e('Select pages you want to block:', 'iqblockcountry'); ?></th>
    <td width="70%">
     
 	<ul>
    <?php
        $selectedpages = get_option('blockcountry_pages'); 
        $pages = get_pages(); 
    foreach ( $pages as $page ) {
      if (is_array($selectedpages)) {
                                if ( in_array( $page->ID,$selectedpages) ) {
                                        $selected = " checked=\"checked\"";
                                } else {
                                        $selected = "";
                                }
                        }
	echo "<li><input type=\"checkbox\" " . $selected . " name=\"blockcountry_pages[]\" value=\"" . $page->ID . "\" id=\"" . $page->post_title . "\" /> <label for=\"" . $page->post_title . "\">" . $page->post_title . "</label></li>"; 	
  }
        ?>
    </td></tr>
    <tr><td></td><td>
	<p class="submit"><input type="submit" class="button-primary"
	value="<?php _e ( 'Save Changes' )?>" /></p>
    </td></tr>	
    </table>	
    </form>

  <?php
}    


                
/*
 * Settings home
 */
function iqblockcountry_settings_home()
{
?>
<h3><?php _e('Statistics', 'iqblockcountry'); ?></h3>

<?php                     $blocked = get_option('blockcountry_backendnrblocks'); ?>
<p><?php echo $blocked; ?> <?php _e('visitors blocked from the backend.', 'iqblockcountry'); ?></p>
<?php                     $blocked = get_option('blockcountry_frontendnrblocks'); ?>
<p><?php echo $blocked; ?> <?php _e('visitors blocked from the frontend.', 'iqblockcountry'); ?></p>

<hr />

<h3><?php _e('Basic Options', 'iqblockcountry'); ?></h3>
        
<form method="post" action="options.php">
    <?php
	settings_fields ( 'iqblockcountry-settings-group' );
        if (!class_exists('GeoIP'))
	{
		include_once("geoip.inc");
	}
	if (class_exists('GeoIP'))
	{
		
            $countrylist = iqblockcountry_get_countries();

            $ip_address = iqblockcountry_get_ipaddress();
            $country = iqblockcountry_check_ipaddress($ip_address);
            if ($country == "Unknown" || $country == "ipv6" || $country == "")
            { $displaycountry = "Unknown"; }
            else { $displaycountry = $countrylist[$country]; }
            
            
	?>

            <script language="javascript" type="text/javascript" src=<?php echo "\"" . CHOSENJS . "\""?>></script>
            <link rel="stylesheet" href=<?php echo "\"" . CHOSENCSS . "\""?> type="text/css" />
            <script>
                        jQuery(document).ready(function(){
			jQuery(".chosen").data("placeholder","Select country...").chosen();
                       });
            </script>
    

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    

            <tr valign="top">
    	    <th width="30%"><?php _e('Message to display when people are blocked:', 'iqblockcountry'); ?></th>
    	    <td width="70%">
    	    <?php
				$blockmessage = get_option ( 'blockcountry_blockmessage' );
				if (empty($blockmessage)) { $blockmessage = "Forbidden - Users from your country are not permitted to browse this site."; }
    	    ?>
                <textarea cols="100" rows="3" name="blockcountry_blockmessage"><?php echo $blockmessage; ?></textarea>
    	    </td></tr>

    	    <tr valign="top">
    	    <th width="30%"><?php _e('Do not block users that are logged in from visiting frontend website:', 'iqblockcountry'); ?></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blocklogin" <?php checked('on', get_option('blockcountry_blocklogin'), true); ?> />
    	    </td></tr>

            <tr valign="top">
            <th width="30%"><?php _e('Block users from visiting the frontend of your website:', 'iqblockcountry'); ?></th>
            <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blockfrontend" <?php checked('on', get_option('blockcountry_blockfrontend'), true); ?> />
            </td></tr>
            
            <tr valign="top">
		<th scope="row" width="30%"><?php _e('Select the countries that should be blocked from visiting your frontend:', 'iqblockcountry'); ?><br />
				<?php _e('Use the CTRL key to select multiple countries', 'iqblockcountry'); ?></th>
		<td width="70%">
                     <select class="chosen" name="blockcountry_banlist[]" multiple="true" style="width:600px;">
                    <?php
			$haystack = get_option('blockcountry_banlist');
			foreach ( $countrylist as $key => $value ) {
			print "<option value=\"$key\"";
			if (is_array($haystack) && in_array ( $key, $haystack )) {
				print " selected=\"selected\" ";
			}
                            print ">$value</option>\n";
                        }   
                        ?>
                     </select>
                </td></tr>
            <tr valign="top">
                <th width="30%"><?php _e('Frontend whitelist IPv4 and/or IPv6 addresses:', 'iqblockcountry'); ?><br /><?php _e('Use a semicolon (;) to separate IP addresses', 'iqblockcountry'); ?></th>
    	    <td width="70%">
    	    <?php
				$frontendwhitelist = get_option ( 'blockcountry_frontendwhitelist' );
    	    ?>
                <textarea cols="70" rows="5" name="blockcountry_frontendwhitelist"><?php echo $frontendwhitelist; ?></textarea>
    	    </td></tr>
            <tr valign="top">
                <th width="30%"><?php _e('Frontend blacklist IPv4 and/or IPv6 addresses:', 'iqblockcountry'); ?><br /><?php _e('Use a semicolon (;) to separate IP addresses', 'iqblockcountry'); ?></th>
    	    <td width="70%">
    	    <?php
				$frontendblacklist = get_option ( 'blockcountry_frontendblacklist' );
    	    ?>
                <textarea cols="70" rows="5" name="blockcountry_frontendblacklist"><?php echo $frontendblacklist; ?></textarea>
    	    </td></tr>
    	    <tr valign="top">
    	    <th width="30%"><?php _e('Block users from visiting the backend (administrator) of your website:', 'iqblockcountry'); ?></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blockbackend" <?php checked('on', get_option('blockcountry_blockbackend'), true); ?> />
            </td></tr>    
            <tr>
                <th width="30%"></th>
                <th width="70%">
                   <?php _e('Your IP address is', 'iqblockcountry'); ?> <i><?php echo $ip_address ?></i>. <?php _e('The country that is listed for this IP address is', 'iqblockcountry'); ?> <em><?php echo $displaycountry ?></em>.<br />  
                      <?php _e('Do <strong>NOT</strong> set the \'Block users from visiting the backend (administrator) of your website\' and also select', 'iqblockcountry'); ?> <?php echo $displaycountry ?> <?php _e('below.', 'iqblockcountry'); ?><br /> 
                      <?php echo "<strong>" . __('You will NOT be able to login the next time if you DO block your own country from visiting the backend.', 'iqblockcountry') . "</strong>"; ?>
                </th>
            </tr>
    	    </td></tr>
            <tr valign="top">
		<th scope="row" width="30%"><?php _e('Select the countries that should be blocked from visiting your backend:', 'iqblockcountry'); ?><br />
                <?php _e('Use the x behind the country to remove a country from this blocklist.', 'iqblockcountry'); ?></th>
		<td width="70%">
        
                    <select class="chosen" name="blockcountry_backendbanlist[]" multiple="true" style="width:600px;">
                    <?php
			$haystack = get_option ( 'blockcountry_backendbanlist' );
			foreach ( $countrylist as $key => $value ) {
			print "<option value=\"$key\"";
			if (is_array($haystack) && in_array ( $key, $haystack )) {
				print " selected=\"selected\" ";
			}
                            print ">$value</option>\n";
                        }   
                        ?>
                     </select>
                </td></tr>
    	    <tr valign="top">
    	    <th width="30%"><?php _e('Send headers when user is blocked:', 'iqblockcountry'); ?><br />
                <em><?php _e('Under normal circumstances you should keep this selected! Only if you have "Cannot modify header information - headers already sent" errors or if you know what you are doing uncheck this.', 'iqblockcountry'); ?></em></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_header" <?php checked('on', get_option('blockcountry_header'), true); ?> />
    	    </td></tr>
                        
		<tr><td></td><td>
						<p class="submit"><input type="submit" class="button-primary"
				value="<?php _e ( 'Save Changes' )?>" /></p>
		</td></tr>	
		</table>	
        </form>
<?php
        }
        else
        {
		print "<p>You are missing the GeoIP class. Perhaps geoip.inc is missing?</p>";	
        }
        
        echo '<p>This product includes GeoLite data created by MaxMind, available from ';
	echo '<a href="http://www.maxmind.com/">http://www.maxmind.com/</a>.</p>';

	echo '<p>If you like this plugin please link back to <a href="http://www.redeo.nl/">redeo.nl</a>! :-)</p>';

}

/*
 * Function: Display logging
 */
function iqblockcountry_settings_logging()
{    
    ?>
   <h3><?php _e('Last 15 blocked visits', 'iqblockcountry'); ?></h3>
   <?php
   global $wpdb;

   $table_name = $wpdb->prefix . "iqblock_logging";
   $format = get_option('date_format') . ' ' . get_option('time_format');
   $countrylist = iqblockcountry_get_countries();
   echo '<table class="widefat">';
   echo '<thead><tr><th>' . __('Date / Time', 'iqblockcountry') . '</th><th>' . __('IP Address', 'iqblockcountry') . '</th><th>' . __('Hostname', 'iqblockcountry') . '</th><th>' . __('URL', 'iqblockcountry') . '</th><th>' . __('Country', 'iqblockcountry') . '</th><th>' . __('Frontend/Backend', 'iqblockcountry') . '</th></tr></thead>';
   
   foreach ($wpdb->get_results( "SELECT * FROM $table_name ORDER BY datetime DESC LIMIT 15" ) as $row)
   {
       $countryimage = "icons/" . strtolower($row->country) . ".png";
       $countryurl = '<img src="' . plugins_url( $countryimage , dirname(__FILE__) ) . '" > ';
       echo "<tbody><tr><td>";
       $datetime = strtotime($row->datetime);
       $mysqldate = date($format, $datetime);
       echo $mysqldate . '</td><td>' . $row->ipaddress . '</td><td>' . gethostbyaddr( $row->ipaddress ) . '</td><td>' . $row->url . '</td><td>' . $countryurl . $countrylist[$row->country] . '<td>';
       if ($row->banned == "F") _e('Frontend', 'iqblockcountry'); else { _e('Backend', 'iqblockcountry'); }
       echo "</td></tr></tbody>";
   }
   echo '</table>';
   
   
   echo '<hr>';
   echo '<h3>' . __('Top countries that are blocked', 'iqblockcountry') . '</h3>';
   echo '<table class="widefat">';
   echo '<thead><tr><th>' . __('Country', 'iqblockcountry') . '</th><th>' . __('# of blocked attempts', 'iqblockcountry') . '</th></tr></thead>';

   foreach ($wpdb->get_results( "SELECT count(country) AS count,country FROM $table_name GROUP BY country ORDER BY count(country) DESC LIMIT 15" ) as $row)
   {
       $countryimage = "icons/" . strtolower($row->country) . ".png";
       $countryurl = '<img src="' . plugins_url( $countryimage , dirname(__FILE__) ) . '" > ';
       echo "<tbody><tr><td>" . $countryurl . $countrylist[$row->country] . "</td><td>" . $row->count . "</td></tr></tbody>";
   }
   echo '</table>';
   
   echo '<hr>';
   echo '<h3>' . __('Top hosts that are blocked', 'iqblockcountry') . '</h3>';
   echo '<table class="widefat">';
   echo '<thead><tr><th>' . __('IP Address', 'iqblockcountry') . '</th><th>' . __('Hostname', 'iqblockcountry') . '</th><th>' . __('# of blocked attempts', 'iqblockcountry') . '</th></tr></thead>';

   foreach ($wpdb->get_results( "SELECT count(ipaddress) AS count,ipaddress FROM $table_name GROUP BY ipaddress ORDER BY count(ipaddress) DESC LIMIT 15" ) as $row)
   {
       echo "<tbody><tr><td>" . $row->ipaddress . "</td><td>" . gethostbyaddr($row->ipaddress) . "</td><td>" . $row->count . "</td></tr></tbody>";
   }
   echo '</table>';

   echo '<hr>';
   echo '<h3>' . __('Top URLs that are blocked', 'iqblockcountry') . '</h3>';
   echo '<table class="widefat">';
   echo '<thead><tr><th>' . __('URL', 'iqblockcountry') . '</th><th>' .  __('# of blocked attempts', 'iqblockcountry') .  '</th></tr></thead>';

   foreach ($wpdb->get_results( "SELECT count(url) AS count,url FROM $table_name GROUP BY url ORDER BY count(url) DESC LIMIT 15" ) as $row)
   {
       echo "<tbody><tr><td>" . $row->url . "</td><td>" . $row->count . "</td></tr></tbody>";
   }
   echo '</table>';
   
}


/*
 * Create the settings page.
 */
function iqblockcountry_settings_page() {
    
    
            if( isset( $_GET[ 'tab' ] ) ) {  
                $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'home';              
            }
            else
            {
                $active_tab = 'home';
            }
        ?>  
          
        <h2 class="nav-tab-wrapper">  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=home" class="nav-tab <?php echo $active_tab == 'home' ? 'nav-tab-active' : ''; ?>"><?php _e('Home', 'iqblockcountry'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=pages" class="nav-tab <?php echo $active_tab == 'pages' ? 'nav-tab-active' : ''; ?>"><?php _e('Pages', 'iqblockcountry'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>"><?php _e('Tools.', 'iqblockcountry'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=logging" class="nav-tab <?php echo $active_tab == 'logging' ? 'nav-tab-active' : ''; ?>"><?php _e('Logging', 'iqblockcountry'); ?></a>  
        </h2>  
  
    
        <div class="wrap">
<h2>iQ Block Country</h2>

        <hr />
        <?php
        if ($active_tab == "tools")
        { 
            iqblockcountry_settings_tools();
        }
        elseif ($active_tab == "logging")
        {    
            iqblockcountry_settings_logging();
        }
        elseif ($active_tab == "pages")
        {    
            iqblockcountry_settings_pages();
        }
        else
        {
             iqblockcountry_settings_home();
        }
	?>	


    <?php
	/* Check if the Geo Database exists otherwise try to download it */
	if (! (file_exists ( IPV4DBFILE ))) {
		?> 
		<hr>
		<p><?php _e('GeoIP database does not exists. Trying to download it...', 'iqblockcountry'); ?></p>
		<?php
		
			iqblockcountry_downloadgeodatabase('4', true);	
			iqblockcountry_downloadgeodatabase('6', true);	
		}
	
}

