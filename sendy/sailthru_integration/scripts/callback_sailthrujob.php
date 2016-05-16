<?php
include_once dirname(__FILE__) . '/../config.php';

if(file_exists(dirname(__FILE__).'/sendylist')){
    $sailthruConfig = new ConfigClass();
    $SailthuManager = $sailthruConfig->loadManagerBySendyList($map_config, file_get_contents('sendylist'));
    if($SailthuManager){
        if(file_exists(dirname(__FILE__).'/status.txt')){
                unlink('status.txt');
        }
        if(!file_exists(dirname(__FILE__).'/download.txt')){
            if(!empty($_REQUEST) && isset($_REQUEST['export_url'])){
                file_put_contents('download.txt', json_encode($_REQUEST));
                $csv_url = $_REQUEST['export_url'];
                $SailthuManager->getSailthruUsersFromJob($csv_url);
                unlink('download.txt');
            }
        }else{
            $request = json_decode(file_get_contents('download.txt'),true);
            $csv_url = $request['export_url'];
            $SailthuManager->getSailthruUsersFromJob($csv_url);
                unlink('download.txt');
        }
    }
    unlink('sendylist');
}
