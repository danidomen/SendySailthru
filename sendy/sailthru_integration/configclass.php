<?php

class ConfigClass{
    
    function onAllListObserver($map_config,$mysqli,$email,$status,$vars = false){
        $qa = 'SELECT * FROM lists, subscribers WHERE subscribers.email = "'.mysqli_real_escape_string($mysqli, $email).'" AND subscribers.list = lists.id';
        $ra = mysqli_query($mysqli, $qa);
       
        $all_lists = '';
        if ($ra)
        {
            while($row = mysqli_fetch_array($ra)) $all_lists .= $row['list'].',';
            $all_lists = substr($all_lists, 0, -1);
        }

        $lists_ids = explode(',', $all_lists);
        foreach($lists_ids as $list_id){
            if($SailthuManager = $this->loadManagerBySendyList($map_config, short($list_id))){
                if($status){
                    $SailthuManager->emailObserver($email,$status);
                }elseif($vars){
                    $user = $SailthuManager->sailthru->getUserByKey($email, 'email',array('keys'=>1));
                    $SailthuManager->sailthru->saveUser($user['keys']['sid'], $vars);
                }
            }
			
        }
    }

    function loadManagerBySendyList($map_config,$sendy_list_id){
        $config = $this->search($map_config, 'sendy_list_id', $sendy_list_id);
        if(is_array($config) && !empty($config)){
            $config = $config[0];
            $SailthuManager = $this->getSailthruManagerFromConfig($config);
            return $SailthuManager;
        }
        return false;
    }

    function globalMailObserver($map_config,$email,$status){
        foreach($map_config as $config){
            $SailthuManager = $this->getSailthruManagerFromConfig($config);
            if($SailthuManager && method_exists($SailthuManager, 'emailObserver')){
                $SailthuManager->emailObserver($email, $status);
            }
        }
    }

    function getSendyConfigFromArray($config){
        return array(
            'api_key' => $config['sendy_api_key'],
            'installation_url' => $config['sendy_installation_url'],
            'list_id' => $config['sendy_list_id']
        );
    }
    
    function getSailthruManagerFromConfig($config){
        $sailthru = new Sailthru_Client($config['sailthru_api_key'], $config['sailthru_api_secret']);
        $sendy_config = $this->getSendyConfigFromArray($config);
        $sendy = new SendyPHP($sendy_config);
        $SailthuManager = new SailthruManager($sendy_config, $sendy, $sailthru, $config['sailthru_list_name']);
        return $SailthuManager;
    }

    function search($array, $key, $value)
    {
        $results = array();
        $this->search_r($array, $key, $value, $results);
        return $results;
    }

    function search_r($array, $key, $value, &$results)
    {
        if (!is_array($array)) {
            return;
        }

        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }

        foreach ($array as $subarray) {
            $this->search_r($subarray, $key, $value, $results);
        }
    }
}
