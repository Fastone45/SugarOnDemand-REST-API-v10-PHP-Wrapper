SugarOnDemand-REST-API-v10-PHP-Wrapper
======================================

Simple PHP wrapper for registering a lead in SugarCRM SugarOnDemand 7.x using the REST v10 API with OAuth2 using PHP cURL. Uses the endpoints /oauth2/token and /Leads/register. http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.2/70_API/Web_Services/10_REST/

SugarCRM SugarOnDemand 7.x REST API v10 (OAuth2) PHP Wrapper

Created: 09/10/2014
	
EndPoints Supported:
/oauth2/token
/Leads/register
	
Purpose: Replace use of Web-To-Lead Forms interface with use of SugarCRM v10 API REST (http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.2/70_API/Web_Services/10_REST/) using Oauth (http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_6.7/02_Application_Framework/Authentication/Oauth/) to register new leads in SugarCRM.
	
Instructions: Send a Form action to sugar-api-wrapper with $_POST data containing the data you want to pass to Sugar. 

References:
http://support.sugarcrm.com/02_Documentation/01_Sugar_Editions/04_Sugar_Professional/Sugar_Professional_6.5/Application_Guide/32_Web_To_Lead_Forms/
http://developer.sugarcrm.com/2013/08/09/creating-an-api-only-user-in-sugarcrm/
http://developer.sugarcrm.com/2014/02/28/sugarcrm-cookbook1/
https://<yourserver>.sugarondemand.com/rest/v10/help
	
Special Notes: Use https vs http, case sensitive endpoint names
