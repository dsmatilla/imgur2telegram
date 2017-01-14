<?php
    $config['limit'] = 10;
    $config['tg_bot_token'] = ''; // Put bot token there (99...99:XX...XX format)
    $config['tg_bot_channel'] = ''; // Channel name to send updates, starting with @
   
    $html = fetchUrl("http://imgur.com/hot/time");
    $urls = array_slice(getUrls($html), 2);
    $sent = getSent();
    $to_send = array_unique(array_slice(array_diff($urls, $sent), 0, $config['limit']));
    setSent($urls);
    
    foreach($to_send as $line) {
        $link = "http://imgur.com".$line;
        $url="https://api.telegram.org/bot".$config['tg_bot_token']."/sendMessage?text=".urlencode($link)."&chat_id=".$config['tg_bot_channel'];
        fetchUrl($url);
        echo $link."\n";
    }

function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function getUrls($string) {
    $regex = '/\/gallery\/[^\"\'\/ ]+/i';
    preg_match_all($regex, $string, $matches);
    return ($matches[0]);
}

function getSent() {
    $sent = array();
    if(file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'sent.txt'))
        $content = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'sent.txt');
    $sent = explode("\n", $content);
    return $sent;
}

function setSent($url_array) {
    file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'sent.txt', implode("\n",$url_array));
}
