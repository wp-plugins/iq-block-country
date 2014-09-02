<?php

/*
 * Schedule tracking if this option was set in the admin panel
 */
function iqblockcountry_schedule_tracking($old_value, $new_value)
{
    $current_schedule = wp_next_scheduled( 'blockcountry_tracking' );
    if ($old_value !== $new_value)
    {
        if ($new_value == '')
        {
            wp_clear_scheduled_hook( 'blockcountry_tracking' );
        }
        elseif ($new_value == 'on' && $current_schedule == FALSE)
        {
            wp_schedule_event( time(), 'hourly', 'blockcountry_tracking' );
        }
    }
}


/*
 * iQ Block send Tracking
 */
function iqblockcountry_tracking()
{
    if (get_option("blockcountry_tracking") == "on")
    {    
        $lasttracked = get_option("blockcountry_lasttrack");
        global $wpdb;

        $table_name = $wpdb->prefix . "iqblock_logging";
    
        $content = array();
        if (!empty($lasttracked))
        {
            $query = "SELECT id,ipaddress,count(ipaddress) as countip FROM $table_name WHERE banned=\"B\" and id > $lasttracked GROUP BY ipaddress ORDER BY id";
        }
        else
        {
            $query = "SELECT id,ipaddress,count(ipaddress) as countip FROM $table_name WHERE banned=\"B\" GROUP BY ipaddress ORDER BY id";
        }
        foreach ($wpdb->get_results( $query ) as $row)
        {
            $newcontent = array('ipaddress' => $row->ipaddress,'count' => $row->countip);
            array_push($content,$newcontent);
            $id = $row->id;
        }
        
        if (!empty($content))
        {
        	$response = wp_remote_post(TRACKINGURL,
                array(
                'body' => $content
                    )
                );

        if (isset($id)) { update_option('blockcountry_lasttrack',$id); }
        }
    }
}



/*
 * iQ Block Retrieve XML file for API blocking
 */
function iqblockcountry_tracking_retrieve_xml()
{
    $url = TRACKINGRETRIEVEURL;
    
    $result = wp_remote_post(
            $url,
            array(
                'body' => array(
                    'api-key' => get_option('blockcountry_apikey') 
                 
                )
            )
        );    
    
    if ( 200 == $result['response']['code'] ) {
	$body = $result['body'];
        $xml = new SimpleXmlElement($body);
        $banlist = array();
        $i=0;
        foreach ($xml->banlist->ipaddress AS $ip)
        {
            array_push($banlist,sprintf('%s',$ip));
            $i++;
        }    
        update_option('blockcountry_backendbanlistip', serialize($banlist));
    }
    
    
}

/*
 * Schedule tracking if this option was set in the admin panel
 */
function iqblockcountry_schedule_retrieving($old_value, $new_value)
{
    $current_schedule = wp_next_scheduled( 'blockcountry_retrievebanlist' );
    if ($old_value !== $new_value)
    {
        if ($new_value == '')
        {
            wp_clear_scheduled_hook( 'blockcountry_retrievebanlist' );
        }
        elseif (!empty($new_value) && $current_schedule == FALSE)
        {
            wp_schedule_event( time(), 'twicedaily', 'blockcountry_retrievebanlist' );
        }
    }
}
