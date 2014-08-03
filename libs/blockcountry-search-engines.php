<?php

global $searchengines;
$searchengines = array(
    "Ask" => "ask jeeves",
    "Bing" => "bingbot",
    "Duck Duck Go" => "duckduckbot",
    "Google" => "googlebot",  
    "Yahoo!" => "yahoo! slurp",
    "Yandex" => "yandexbot"
);

function iqblockcountry_check_searchengine($user_agent,$allowse)
{
    global $searchengines;
    $issearchengine = FALSE;
    foreach ( $searchengines AS $se => $seua ) {
        if (is_array($allowse) && in_array($se,$allowse))
        {        
            if(stripos($user_agent, $seua) !== false) 
            {
                $issearchengine = TRUE;
            }
        }
    }
    return $issearchengine;
}

?>
