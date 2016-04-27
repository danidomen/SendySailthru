<?php include('../_connect.php');?>
<?php include('../../includes/helpers/short.php');?>
<?php 
        include_once dirname(__FILE__) . '/../../sailthru_integration/config.php';
        
	//-------------------------- ERRORS -------------------------//
	$error_core = array('No data passed', 'API key not passed', 'Invalid API key','No Sailthru Integration Exists');
	$error_passed = array('List ID not passed', 'List does not exist','Your IP is not allowed','Method parameter not passed or not numeric');
	//-----------------------------------------------------------//
        
        if(!function_exists('whitelistIPs')){
            echoError($error_core[3]);
            exit;
        }else{
            if(!whitelistIPs()){
                echoError($error_passed[2]);
                exit;
            }
        }
	
	//--------------------------- POST --------------------------//
	//api_key
	if(isset($_POST['api_key'])) $api_key = mysqli_real_escape_string($mysqli, $_POST['api_key']);
	else $api_key = null;
	
	//list_id
	if(isset($_POST['list_id'])) $list_id = short(mysqli_real_escape_string($mysqli, $_POST['list_id']), true);
	else $list_id = null;
        
        //method
        if(isset($_POST['method'])) $method = mysqli_real_escape_string($mysqli, $_POST['method']);
	else $method = null;
	//-----------------------------------------------------------//
	
	//----------------------- VERIFICATION ----------------------//
	//Core data
	if($api_key==null && $list_id==null)
	{
		echoError($error_core[0]);
		exit;
	}
	if($api_key==null)
	{
		echoError($error_core[1]);
		exit;
	}
	else if(!verify_api_key($api_key))
	{
		echoError($error_core[2]);
		exit;
	}
	
	//Passed data
	if($list_id==null)
	{
		echoError($error_passed[0]);
		exit;
	}
	else
	{
		$q = 'SELECT id FROM lists WHERE id = '.$list_id;
		$r = mysqli_query($mysqli, $q);
		if (mysqli_num_rows($r) == 0) 
		{
			echoError($error_passed[1].' - '.$list_id); 
			exit;
		}
	}
        
        if(empty($method) || !is_numeric($method)){
            echoError($error_passed[3]); 
            exit;
        }
        
	//-----------------------------------------------------------//
	
	//-------------------------- QUERY --------------------------//
	$unsubscribed = 0; $bounced = 0; $complaint = 0; $confirmed = 0; $all = 0;        
        switch($method){
            case 1: $confirmed = 1; break;
            case 2: $unsubscribed = 1; break;
            case 3: $bounced = 1; break;
            case 4: $complaint = 1; break;
            case 5: $all = 1; break;
            default : $confirmed = 1; break;
        }
	$q = 'SELECT * FROM subscribers WHERE '.(($all == 1)?'':'(unsubscribed='.$unsubscribed.' AND bounced='.$bounced.' AND complaint='.$unsubscribed.' AND confirmed='.$confirmed.') AND').' list = '.$list_id;
        $r = mysqli_query($mysqli, $q);
	$jsonData = array();
        if ($r && mysqli_num_rows($r) > 0)
	{	    
            while ($array = mysqli_fetch_array($r)) {
                $jsonData[] = $array;
            } 
	}
        echo json_encode($jsonData);  
	//-----------------------------------------------------------//
        
        
        function echoError($error){
            echo json_encode(array('error'=>$error));
        }
?>