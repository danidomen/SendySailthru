<?php
if(!class_exists('SendyPHP')){
    include_once dirname(__FILE__) . '/../sendy/SendyPHP.php';
}
if(!class_exists('Sailthru_Client')){
    include_once dirname(__FILE__) . '/../sailthru/Sailthru_Client.php';
    include_once dirname(__FILE__) . '/../sailthru/Sailthru_Client_Exception.php';
    include_once dirname(__FILE__) . '/../sailthru/Sailthru_Util.php';
}
if(!class_exists('SailthruManager')){
    include_once dirname(__FILE__) . '/../manager.php';
}
if(!class_exists('ConfigClass')){
    include_once dirname(__FILE__) . '/../configclass.php';
}

if(!function_exists('short')){
    include_once dirname(__FILE__) . '/../../includes/helpers/short.php';
}
