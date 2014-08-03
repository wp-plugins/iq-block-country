<?php

/*
 * iQ Block Tracking
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
