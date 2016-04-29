<?php
include_once dirname(__FILE__) . '/../config.php';

if(isset($_GET['token']) && $_GET['token'] == $cronjobtoken){
    $SailthuManager->sailthru2sendyList();
}else{
    die('Not allowed');
}
