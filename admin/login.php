<?php
/*
 * For allowing users to register with the site.  This will also provide activation of new users if applicable.
 */

define('LOGIN_SUCCESS', "You have been successfully logged in.");
define('LOGIN_NEED_ACTIVATION', "The administrator will need to activate your account.");
define('LOGIN_FAILURE', "Unable to login the username: %s");
define('LOGOUT_SUCCESS', "You have been logged out of the system.");

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
	$returnData = null;
	
	$userInstance = UserSession::getInstance();
	
	$success = false;
	$statusMessage = "none";
	$userName = "none";
	
	/* If they are getting then we need to display the form below */
	if (isset($_GET['option']) == false)
	{
		/*
		 *  Logging in a user to the system
		 */
		if (isset($_POST['userName']) && isset($_POST['userPassword']))
		{	
			$systemObject = System::getInstance();
				
			$statusMessage = sprintf(LOGIN_FAILURE, $_POST['userName']);
	
			// the index page has already processed the login in by default
			// see if it was successful		
			if ($userInstance->isLoggedIn() == true)
			{
				$userInstance->updateActivity();
				$userName = $_POST['userName'];
				$success = true;
				$statusMessage = LOGIN_SUCCESS;
			}
			elseif ($userInstance->isActive() == false)
			{
				$statusMessage = LOGIN_NEED_ACTIVATION;
			}
		}
		else
		{
			$userInstance->logoutUser();
			$success = true;
			$statusMessage = LOGOUT_SUCCESS;
		}
			
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
		
		$xmlNode = new XmlDataObject();
		$xmlNode->setName("userName");
		$xmlNode->setValue($userName);
		$xmlPage->addChildObject($xmlNode);
		
		// We know this an xml file so ensure that we set the right header before dumping the file
	    header("Content-Type: application/xhtml+xml;charset=iso-8859-1");
		$returnData = $xmlPage->renderPage();
	}	
	return $returnData;
}
?>