<?php
include_once dirname(__FILE__) . '/../config.php';

if(isset($_GET['token']) && $_GET['token'] == $cronjobtoken){
    if(isset($_GET['sendylist']) && !empty($_GET['sendylist'])){
        $sailthruConfig = new ConfigClass();
        $SailthuManager = $sailthruConfig->loadManagerBySendyList($map_config, $_GET['sendylist']);
        file_put_contents('sendylist', $_GET['sendylist']);
        if($SailthuManager){
            $SailthuManager->sailthru2sendyList();
        }
    }else{
        die('Not sendy list provided');
    }
}else{
    die('Not allowed');
}
