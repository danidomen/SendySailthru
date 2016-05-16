<?php include('../functions.php');?>
<?php include('../login/auth.php');?>
<?php
include_once dirname(__FILE__) . '/../../sailthru_integration/config.php';

	$subscriber_id = mysqli_real_escape_string($mysqli, $_POST['subscriber_id']);
	$action = $_POST['action'];
	$time = time();
	
	if($action=='unsubscribe')
		$q = 'UPDATE subscribers SET unsubscribed = 1, timestamp = '.$time.' WHERE id = '.$subscriber_id.' AND userID = '.get_app_info('main_userID');
	else if($action=='resubscribe')
		$q = 'UPDATE subscribers SET unsubscribed = 0, timestamp = '.$time.' WHERE id = '.$subscriber_id.' AND userID = '.get_app_info('main_userID');
	else if($action=='confirm')
		$q = 'UPDATE subscribers SET confirmed = 1, timestamp = '.$time.' WHERE id = '.$subscriber_id.' AND userID = '.get_app_info('main_userID');
	$r = mysqli_query($mysqli, $q);
	if ($r)
	{
                $q1 = 'SELECT email FROM subscribers WHERE id = '.$subscriber_id.' LIMIT 1';
		$r1 = mysqli_query($mysqli, $q1);
		if ($r1)
		{
                    $row1 = mysqli_fetch_array($r1);
                    if(isset($row1['email'])){
                        $sailthruConfig = new ConfigClass();
                        switch($action){
                            case 'unsubscribe': $status = 'unsubscribed'; break;
                            case 'resubscribe': $status = 'resubscribe'; break;
                            case 'confirm': $status = 'confirm'; break;
                                
                        }
                        $sailthruConfig->onAllListObserver($map_config,$mysqli, $row1['email'],$status);
                    }
                }
		echo true; 
	}
	
?>