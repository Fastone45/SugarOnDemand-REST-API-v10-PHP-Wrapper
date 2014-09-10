<?php

/* 	

	SugarCRM SugarOnDemand 7.x REST API v10 (OAuth2) PHP Wrapper

	Created: 09/10/2014
	
	EndPoints Supported:
	/oauth2/token
	/Leads/register
	
	Purpose: Replace use of Web-To-Lead Forms interface (http://support.sugarcrm.com/02_Documentation/01_Sugar_Editions/04_Sugar_Professional/Sugar_Professional_6.5/Application_Guide/32_Web_To_Lead_Forms/)
	with use of SugarCRM v10 API REST (http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.2/70_API/Web_Services/10_REST/) using Oauth (http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_6.7/02_Application_Framework/Authentication/Oauth/)
	to register new leads in SugarCRM.
	
	Insturctions: Send a Form action to sugar-api-wrapper with $_POST data containing the data you want to pass to Sugar. 

	Additional references:
	http://developer.sugarcrm.com/2013/08/09/creating-an-api-only-user-in-sugarcrm/
	http://developer.sugarcrm.com/2014/02/28/sugarcrm-cookbook1/
	https://<yourserver>.sugarondemand.com/rest/v10/help
	
	Special Notes: Use https vs http, case sensitive endpoint names
	
*/

$base_url = "https://******.sugarondemand.com/rest/v10";
$grant_type = "password";
$client_id = "sugar";
$client_secret = "";
$sugar_username = "******";
$sugar_password = "******";
$redirect_URL = $_POST[redirect_url];

function getToken() {
		
		global $base_url, $grant_type, $client_id, $client_secret, $sugar_username, $sugar_password;
		
		$data = array(
        	"grant_type"=>$grant_type,
        	"client_id"=>$client_id,
        	"client_secret"=>$client_secret,
        	"username"=>$sugar_username,
        	"password"=>$sugar_password
    		);

		$payload = json_encode($data);

		$submit_url = $base_url."/oauth2/token";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $submit_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

		$result = curl_exec($ch);
		curl_close ($ch);
		
		//print_r($result);
		
		// For Debugging
		$data = json_decode($result);
		/*
		if ($data->error){
			echo $data->code .' : '.$data->error."\n";
		} else {
			echo "No error found";
		}*/
		return $data->access_token;
			
}

function registerLead($access_token) {

		global $base_url;

		$payload ='{';
		foreach ( $_POST as $key => $value) {
			$payload .= '"'.$key.'":'.'"'.$value.'",';
		}		
		$payload .= '"email":[{"email_address":"'.$_POST["webtolead_email1"].'"}]}';

		//print_r($payload);
		$submit_url = $base_url."/Leads/register";

		$ch = curl_init($submit_url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',"OAuth-Token: $access_token"));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

		$result = curl_exec($ch);
		curl_close ($ch);
		
		//print_r($result);
		
		//For Debugging
		//$data = json_decode($result);
		
		/*
		if ($data->error){
			echo $data->code .' : '.$data->error."\n";
		} else {
			echo "No error found";
		}*/
			
}


if ($_POST['account_name']) {
	registerLead(getToken());
	header('Location: '.$redirect_URL);
}

?>
