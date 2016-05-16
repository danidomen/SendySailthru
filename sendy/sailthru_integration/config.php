<?php
include_once dirname(__FILE__) . '/scripts/include_classes.php';

/*
 * CRONJOB TOKEN
 */
$cronjobtoken = 'ABCDEFG123456789';

/*
 * SENDY CONFIG
 */
$sendy_config = array(
    'api_key' => 'yousendyapikey', //your API key is available in Settings
    'installation_url' => 'http://yousendy.com',  //Your Sendy installation
);

/**
 * MULTIMAP CONFIGURATION
 */
$map_config[] = array(
    'sendy_api_key' => $sendy_config['api_key'], 
    'sendy_installation_url' => $sendy_config['installation_url'],
    'sendy_list_id' => 'yoursendylistidtobesynced', //Example: qrgJGmS8892ipLs8gUQ2MzLw
    'sailthru_api_key' => "yoursailthruapikey", //Your Sailthru Api Key
    'sailthru_api_secret' => "yoursailthruapisecret", //Your Sailthru Api Secret
    'sailthru_list_name' => 'TEST_SENDY_SYNC' //Name of Sailthru list to sync with sendy emails
);

// To add another list config, you only need to copy the code below out of the comments and fill with your data
/*
$map_config[] = array(
    'sendy_api_key' => $sendy_config['api_key'], 
    'sendy_installation_url' => $sendy_config['installation_url'],
    'sendy_list_id' => 'yoursendylistidtobesynced', //Example: qrgJGmS8892ipLs8gUQ2MzLw
    'sailthru_api_key' => "yoursailthruapikey", //Your Sailthru Api Key
    'sailthru_api_secret' => "yoursailthruapisecret", //Your Sailthru Api Secret
    'sailthru_list_name' => 'TEST_SENDY_SYNC' //Name of Sailthru list to sync with sendy emails
);
*/

/**
 * FUNCTION ALLOWED IPS 
 */
function whitelistIPs(){
    $whitelist = array(
        '127.0.0.1',
        '::1',
        '88.198.19.100',
    );
    if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
        return true;
    }
    return false;
}


