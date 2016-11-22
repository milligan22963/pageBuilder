<?php
/*
 * processAdminOptions
 * 
 * Used to process any incoming admin options related to this page
 * 
 * @param - the page to add data to
 * 
 * @return null if nothing to do otherwise a page rendering for returning to the user
 */
function processAdminOptions(& $page)
{
	$systemObject = System::getInstance();
	
	$returnData = null;
	$generateResponse = false;
	$success = false;
	
	/*
	 *  if the user has admin priviledges and has the correct hash then
	 *  will process the options specific to the user i.e. register, activate, de-activate, delete
	 *  
	 */
	if (isset($_POST['userName']) && isset($_POST['userPassword']))
	{
		$generateResponse = true;	
	}
	elseif (isset($_GET['userName']) && isset($_GET['activate']) && isset($_GET['key']))
	{
		$generateResponse = true;
	}
	
	if ($generateResponse == true)
	{	
		$xmlPage = new XmlPageData();
		$xmlPage->setDirectDisplay(false);
		
		$xmlPage->setName('admin');
		
		$xmlNode = new XmlDataObject();
		$xmlNode->setName('results');

		if ($success == true)
		{
		  $xmlNode->setValue('1');
		}
		else
		{
		  $xmlNode->setValue('0');
		}
		$xmlPage->addChildObject($xmlNode);
		
		$xmlNode = new XmlDataObject();
		$xmlNode->setName('returnString');
		$xmlNode->setValue($statusMessage);
		$xmlPage->addChildObject($xmlNode);
		
		// We know this an xml file so ensure that we set the right header before dumping the file
        header("Content-Type: application/xhtml+xml;charset=iso-8859-1");
		$returnData = $xmlPage->renderPage();
	}
	
	return $returnData;
}

/*
 * getAdminBodyData
 * 
 * Used to get the body data associated to this aspect of the admin page
 * 
 * @param - the page to add data to
 * 
 * @return none 
 */
function getAdminBodyData(& $page, $userType)
{
	$systemObject = getSystemObject();
	
	/*
	 * Get the admin page data for registering a user
	 */
	loadScript("JQUERY", null, $page);
	
	// We are in the admin directory so need to get the baseScript url which will be our current path
	$registerString = $systemObject->getBaseScriptURL() . "index.php";
	
	// Add in our javascript file
	$javaScriptFile = $systemObject->getBaseScriptURL() . "js/register.js";
	$page->addJavaScriptLink($javaScriptFile);
	$page->addBodyData("User Options: <br />");
	
	/*
	 * This if for registering new users
	 */
	$registerForm = <<< REGISTER_FORM_DATA
	<div class="registeruser">
  		<form id="userregister" class="register" method="post" action="javascript:registerUser('$registerString', 'registerUserName', 'registerUserPassword', 'registerUserEmail');">
  		    <label id="registerUserNameLbl">UserName:
  			<input type="text" id="registerUserName" /></label>
  			<label id="registerUserPasswordLbl">Password:
  		    <input type="password" id="registerUserPassword" /></label>
  			<label id="registerUserEmailLbl">Password:
  		    <input type="text" id="registerUserEmail" /></label>
  		    <input type="submit" value="Register" />
  		</form>
	</div>
REGISTER_FORM_DATA;

	$page->addBodyData($registerForm);
	
	/*
	 * Create an activate option
	 */
	
	/*
	 * Create a de-activate option
	 */
	
	/*
	 * Create a delete option i.e. disable
	 */
}
?>