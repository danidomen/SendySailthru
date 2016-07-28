<?php

class SailthruManager{
    
    var $sendy_config;
    var $sendy;
    var $sailthru;
    var $sailthru_list;
    
    function __construct($_sendy_config,$_sendy,$_sailthru,$_sailthru_list)
    {
        $this->sendy_config = $_sendy_config;
        $this->sendy = $_sendy;
        $this->sailthru = $_sailthru;
        $this->sailthru_list = $_sailthru_list;
    }
    /**
     * Method to sync a Sendy list to a Sailthru list. The values can be setted on config.php
     */
    function sendy2sailthruList(){
        $sendy_list = $this->sendy->sublist($this->sendy_config['list_id'],5);
        $sendy_subscribers = json_decode($sendy_list['message'],true);
        $processed = 0;
        if(is_array($sendy_subscribers) && !isset($sendy_subscribers['error'])){
            foreach($sendy_subscribers as $subs){
                $sendy_status = $this->sendyRowStatusToString($subs);
                $result = $this->emailObserver($subs['email'], $sendy_status, array($this->sailthru_list => 1), (int)$subs['confirmed']);
                $processed++;
            }
			echo 'Emails Processed: '.$processed;
        }else{
            echo 'No sendy subscribers found. Please check that all credentials and IPs are seted correctly on config.php <br/> '.((isset($sendy_list['message']) && !empty($sendy_list['message']))?'Real Error: '.$sendy_list['message']:'');
        }
    }
    
    /**
     * Method to sync a Sailthru list to a Sendy list. The values can be setted on config.php
     */
    function sailthru2sendyList(){
        $sailthru_emails = $this->sailthru->processExportListJob($this->sailthru_list,false,$this->sendy_config['installation_url'].'/sailthru_integration/scripts/callback_sailthrujob.php');
        echo '<pre>'; var_dump($sailthru_emails); echo '</pre>';
    }
    
    function getSailthruUsersFromJob($content_url){
        $data = file_get_contents($content_url);
        
        $Data = array_map("str_getcsv", preg_split('/\r*\n+|\r+/', $data));
        $header = true;
        foreach ($Data as &$Row){
            if ($header) {
                $header = false;
            } else {
                //$Row = str_getcsv($Row, ','); //parse the items in rows
                $sailthru_user = $this->sailthru->getUserBySid($Row[0]);
                
                if(isset($sailthru_user['keys']['email'])){
                    if(isset($sailthru_user['optout_email']) && !empty($sailthru_user['optout_email']) && $sailthru_user['optout_email'] != 'none'){
                        $this->sendy->subscribe(array(
                            'email' => $sailthru_user['keys']['email'], //this is the only field required by sendy
                        ));
                        $this->sendy->unsubscribe($sailthru_user['keys']['email']);
                    }else{
                        $this->sendy->subscribe(array(
                            'email' => $sailthru_user['keys']['email'], //this is the only field required by sendy
                        ));
                    }
                    
                    
                }

            }
        }
    }
    
    /**
     * Method to sync a Sendy email to Sailthru email
     * @param type $email
     * @param type $status
     * @param type $to_sailthru_list
     * @param type $confirmed
     * @return type
     */
    function emailObserver($email,$status,$to_sailthru_list = array(),$confirmed = 1,$extended_vars = false){
        $optout = 'none';
        switch($status){
            case 'unsubscribed': $optout = 'basic'; break;
            case 'bounced': $optout = 'blast'; break;
            case 'bounce_soft': $optout = 'basic'; break;
            case 'complaint': $optout = 'all'; break;
            default: $optout = 'none'; break;
        }
        $vars = array('sendy_status'=>$status);
        if($extended_vars && is_array($extended_vars)){
            $vars = array_merge($vars,$extended_vars);
        }
        $result = $this->sailthru->setEmail($email, $vars, $to_sailthru_list,array(),(int)$confirmed,$optout);
        return $result;
    }
    
    /**
     * Helper to extract the status string from a sendy db row
     * @param type $row
     * @return string
     */
    function sendyRowStatusToString($row){
        $sendy_status = 'active';
        if((int)$row['unsubscribed'] == 1){
            $sendy_status = 'unsubscribed';
        }
        if((int)$row['bounced'] == 1){
            $sendy_status = 'bounced';
        }
        if((int)$row['bounce_soft'] == 1){
            $sendy_status = 'bounce_soft';
        }
        if((int)$row['complaint'] == 1){
            $sendy_status = 'complaint';
        }
        return $sendy_status;
    }
}


