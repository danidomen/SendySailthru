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
    'list_id' => 'yoursendylistidtobesynced' //Example: qrgJGmS8892ipLs8gUQ2MzLw
);

/**
 * SAILTHRU CONFIG
 */
$sailthru_api_key = "yoursailthruapikey"; //Your Sailthru Api Key
$sailthru_api_secret = "yoursailthruapisecret"; //Your Sailthru Api Secret
$sailthru_list = 'TEST_SENDY_SYNC'; //Name of Sailthru list to sync with sendy emails


/**
 * FUNCTION ALLOWED IPS 
 */
function whitelistIPs(){
    $whitelist = array(
        '127.0.0.1',
        '::1',
        '88.198.19.132',
    );
    if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
        return true;
    }
    return false;
}


$sailthru = new Sailthru_Client($sailthru_api_key, $sailthru_api_secret);
$sendy = new SendyPHP($sendy_config);

$SailthuManager = new SailthruManager($sendy_config, $sendy, $sailthru, $sailthru_list);


