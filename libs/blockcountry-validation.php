<?php

/*
 * Check of an IP address is a valid IPv4 address
 */
function iqblockcountry_is_valid_ipv4($ipv4) 
{
    if(filter_var($ipv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE) {
        return false;
    }

    return true;
}

/*
 * Check of an IP address is a valid IPv6 address
 */
function iqblockcountry_is_valid_ipv6($ipv6) 
{
    if(filter_var($ipv6, FILTER_VALIDATE_IP,FILTER_FLAG_IPV6) === FALSE) {
	return false;
    }

    return true;
}


 /*
  * Sanitize callback. Check if supplied IP address list is valid IPv4 or IPv6
  */
function iqblockcountry_validate_ip($input)
{
    $validips = "";
    if (preg_match('/;/',$input))
    {
        $arr = explode(";", $input);
        foreach ($arr as $value) {
            if (iqblockcountry_is_valid_ipv4($value) || iqblockcountry_is_valid_ipv6($value))
            {
                $validips .= $value . ";";
            }
            
        }
    }
    else
    {
        if (iqblockcountry_is_valid_ipv4($input) || iqblockcountry_is_valid_ipv6($input))
        {
            $validips = $input . ";";
        }
    }
    return $validips;
    
}

