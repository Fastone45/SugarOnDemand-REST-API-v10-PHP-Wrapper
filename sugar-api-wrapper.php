<?php

/* 	

	SugarCRM SugarOnDemand 7.x REST API v10 (OAuth2) PHP Wrapper

	Last Updated: 09/19/2014
	
	EndPoints Supported:
	/oauth2/token
	/Leads/register
	/<module>/:record
	
	Purpose: Replace use of Web-To-Lead Forms interface with use of SugarCRM API REST v10 (using Oauth 2)
	to register new leads. Once the script has received your data, it will initiate a call to the SugarCRM API
	and get a new access token. It will then use the token to import the new lead and if you have emails turned on,
	it will email you to let you know the lead has been added. Because that endpoint does not accept certain fields
	for new leads (including a custom field), the script then finds the new lead in Sugar and uses another endpoint
	to update the lead with additional field information. If emails are turned on and there is an error, you will
	be notified of errors from the API via email.
	
	Insturctions: Update with your credintials below. Send a form action to sugar-api-wrapper with $_POST data containing the data you want to pass to Sugar. 

	References:
	http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.2/70_API/Web_Services/10_REST/
	http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_6.7/02_Application_Framework/Authentication/Oauth/
	http://support.sugarcrm.com/02_Documentation/01_Sugar_Editions/04_Sugar_Professional/Sugar_Professional_6.5/Application_Guide/32_Web_To_Lead_Forms/
	http://developer.sugarcrm.com/2013/08/09/creating-an-api-only-user-in-sugarcrm/
	http://developer.sugarcrm.com/2014/02/28/sugarcrm-cookbook1/
	https://<yourserver>.sugarondemand.com/rest/v10/help
	
	Special Notes: Use https vs http, case sensitive endpoint names, has to update the lead for the custom field to be saved
	
*/

$base_url = 'https://******.sugarondemand.com';
$grant_type = 'password';
$client_id = 'sugar';
$client_secret = '';
$sugar_username = '******';
$sugar_password = '******';
$send_emails = 'YES';
$email_to = '******@******.***';
$redirect_URL = $_POST[redirect_url];

function call($submit_url, $type, $access_token, $payload) {

		global $send_emails;
		
		// For Debugging:
		/*echo '<br>Submit URL:';
		print_r($submit_url);
		echo '<br>Type:';
		print_r($type);
		echo '<br>Access Token:';
		print_r($access_token);
		echo '<br>Payload:';
		print_r($payload);*/
		

		$ch = curl_init($submit_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if ($type == 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
		} elseif ($type == 'PUT') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		}
		
		if ($access_token) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',"OAuth-Token: $access_token"));
		}
		
		if ($payload) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		}

		$result = curl_exec($ch);
		curl_close ($ch);
		
		// For Debugging:
		/*echo '<br>Result:';
		print_r($result);*/
		
		$data = json_decode($result);
		
		if ($data->error AND $send_emails == 'YES'){
			
			global $email_to;

			$subject = "SugarCRM API Error";
			$messsage = "\r\n sugar-api-wrapper.php received an error while attempting to access SugarCRM.\r\n\r\n".$data->error .' : '.$data->error_message."\n";
		
			mail($email_to, $subject, $messsage);
		
		}
		
		return $data;
		

}

function getToken() {
		
		global $base_url, $grant_type, $client_id, $client_secret, $sugar_username, $sugar_password;
		
		$data = array(
        'grant_type'=>$grant_type,
        'client_id'=>$client_id,
        'client_secret'=>$client_secret,
        'username'=>$sugar_username,
        'password'=>$sugar_password
    	);

		$payload = json_encode($data);

		$submit_url = $base_url.'/rest/v10/oauth2/token';
		
		$data = call($submit_url, 'POST','',$payload);
		
		return $data->access_token;
			
}

function registerLead($access_token) {

		global $base_url, $email_to, $send_emails;

		$payload ='{';
		foreach ( $_POST as $key => $value) {
			$payload .= '"'.$key.'":'.'"'.$value.'",';
		}		
		$payload .= '"email":[{"email_address":"'.$_POST['webtolead_email1'].'"}]}';

		$submit_url = $base_url.'/rest/v10/Leads/register';
		
		$data = call($submit_url,'POST',$access_token,$payload);
		
		if ($send_emails == 'YES') {
			$leadname = $_POST['first_name'].' '.$_POST['last_name'];
			$leadurl = $base_url."/index.php?module=Leads&action=DetailView&record=$data->id";
			$subject = "SugarCRM Lead - $leadname";
			$messsage = "\r\n A new lead has been created in SugarCRM by sugar-api-wrapper.php.\r\n\r\nName: $leadname\r\nLead Source: sugar-api-wrapper.php\r\n\r\n$leadurl";
		
			mail($email_to, $subject, $messsage);
		
		}
		
		return $data->id;
}

function updateLead($access_token, $id) {

		global $base_url;

		$data = array(
        'generate_c'=>$_POST['generate_c']
    	);

		$payload = json_encode($data);

		$submit_url = $base_url.'/rest/v10/Leads/'.$id;

		$data = call($submit_url, 'PUT', $access_token, $payload);
		
}

if ($_POST['account_name']) {

		$access_token = getToken();
		$id = registerLead($access_token);
		updateLead($access_token, $id);
		header('Location: '.$redirect_URL);
	
} else {

	echo 'No data received.';
	
}

?>