<?php

$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . '/toolbox/mailer.php';

/*
 * For allowing users to register with the site.  This will also provide activation of new users if applicable.
 */

define('REGISTER_SUCCESS', "You have been registered with this site.");
define('REGISTER_NEED_ACTIVATION', "The administrator will need to activate your account.");
define('REGISTER_USER_ACTIVATION', "You will receive an email with activation details for your account.");
define('REGISTER_FAILURE', "Unable to register the username: %s");
define('REGISTER_ACTIVATE', "The user: %s has been activated.");
define('REGISTER_ACTIVATE_FAIL', "Unable to activate your account.");
define('REGISTER_ALREADY_ACTIVATED', "The user: %s has already been activated.");
define('REGISTER_LOGIN', "You must login to activate this user.");
define('REGISTER_DISALLOW_USERS', "User's are not allowed to register");

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
	 *  if the option for registering a user is set then process and return an alternate rendering
	 *  This will do the basics i.e. username and password.  Once registered and activated
	 *  the user can be asked to enhance their profile based on the overall site needs which
	 *  is beyond the scope of the basic system
	 *  
	 *  If an email is required, it will be gathered below depending on the options specified in
	 *  config.xml
	 */
	if (isset($_POST['userName']) && isset($_POST['userPassword']))
	{
		$generateResponse = true;
	
		$statusMessage = REGISTER_DISALLOW_USERS;
		
		$allowNewUsers = $systemObject->getConfigurationData(SITE_ALLOW_NEW_USERS, "bool");
		$allowAdminCreateNewUsers = $systemObject->getConfigurationData(SITE_ALLOW_ADMIN_CREATE_USERS, "bool");
		
		/*
		 * If we allow the admin to create new users and oh by the way this happens to be the admin then
		 * by all means let him pass
		 */
		if (($allowNewUsers == false) && ($allowAdminCreateNewUsers == true))
		{
			$userSession = UserSession::getInstance();
			
			if ($userSession->getUserType() == USER_TYPE_ADMIN)
			{
				$allowNewUsers = true;
			}
		}
	
		if ($allowNewUsers == true)
		{
			$statusMessage = sprintf(REGISTER_FAILURE, $_POST['userName']);
			
			/*
			 * See if this user exists, if not then attempt to create them
			 */
			if (User::userExists($_POST['userName']) == false)
			{
				/*
				 * See if this is a single step or two step process
				 * Also determine if the user can activate themselves or
				 * does it require admin permission
				 */
				$autoActivate = $systemObject->getConfigurationData(SITE_AUTO_ACTIVATE_REGISTRATION, "bool");
				
				$userObject = User::createUser($_POST['userName'], $_POST['userPassword'], 'user', $autoActivate);
				
				/*
				 * If an object was returned then we were successful, otherwise failure
				 */
				if ($userObject != null)
				{
					$statusMessage = REGISTER_SUCCESS;

					/*
					 * See if an email was provided if so then store it
					 * 
					 * Some sites may require an email and some not, should force the interface
					 * to handle those requirements
					 */
					if (isset($_POST['userEmail']))
					{
						$userObject->setUserEmail($_POST['userEmail']);
						$userObject->saveUser();
					}
					
					/* If autoactivate is true then they are already activated at time of creation so we are done*/
					if ($autoActivate == false)
					{
						$userActivation = $systemObject->getConfigurationData(SITE_USER_ACTIVATION);
						
						$activationText = $systemObject->getConfigurationData(SITE_TITLE);
						
						$activationMessage = new Mail();
						
						$activationMessage->isHtml(false);
						$activationMessage->setSender($systemObject->getConfigurationData(SITE_ADMIN_EMAIL));
						
						$subject = $systemObject->getConfigurationData(SITE_TITLE) . " - Registration Activation";
						$activationMessage->setSubject($subject);
						
						$activationLink = $systemObject->getBaseScriptURL() . "index.php?option=register&userName=" . $_POST['userName'];
						
						/*
						 * Generate a registration key for the user to validate against
						 */
						$key = User::generateRegistrationKey($_POST['userName'], $userObject->getUserId());
						$activationLink .= "&activate=true&key=" . $key;
						
						if ($userActivation == true)
						{
							// Notify them via email that they are registered and need to activate
							// else the admin will need to activate them
							$statusMessage .= REGISTER_USER_ACTIVATION;
							
							$activationText .= " - You have been registered please click the following link to activate your account: " . PHP_EOL;
							$activationText .= $activationLink;
							$activationMessage->addRecipient($userObject->getUserEmail());
						}
						else
						{
							$activationText .= "- A new user: " . $_POST['userName'] . " has been registered.  Please activate as soon as possible." . PHP_EOL;
							$activationText .= $activationLink;
							$activationMessage->addRecipient($systemObject->getConfigurationData(SITE_ADMIN_EMAIL));
							$statusMessage .= REGISTER_NEED_ACTIVATION;
						}
						$activationMessage->setMessage($activationText);
						$activationMessage->sendMessage();
					}

					$success = true;
				}
			}
//			$statusMessage = $_POST['userName'] . $_POST['userPassword'];
		}
	}
	elseif (isset($_GET['userName']) && isset($_GET['activate']) && isset($_GET['key']))
	{
		$generateResponse = true;

		// Activate this user
		$autoActivate = $systemObject->getConfigurationData(SITE_AUTO_ACTIVATE_REGISTRATION, "bool");
		
		/* If autoactivate is true then they are already activated at time of creation so we are done*/
		if ($autoActivate == false)
		{
			$continueActivation = true;
			$userActivation = $systemObject->getConfigurationData(SITE_USER_ACTIVATION, "bool");
			
			if ($userActivation == false)
			{
				// Admin only, is current user an admin?
				$userSession = UserSession::getInstance();
				
				if ($userSession->getUserType() != USER_TYPE_ADMIN)
				{
					if ($userSession->isLoggedIn() == true)
					{
						$statusMessage = REGISTER_NEED_ACTIVATION;
					}
					else
					{
						$statusMessage = REGISTER_LOGIN;
					}
					$continueActivation = false;
				}
			}
			
			if ($continueActivation == true)
			{
				$newUser = new User();
				
				$newUser->loadUserByUserName($_GET['userName']);
				
				if ($newUser->getUserValid() == true)
				{
					// They are a valid user
					
					if ($newUser->getUserActive() == false)
					{
						// Validate key
						if ($_GET['key'] == User::generateRegistrationKey($_GET['userName'], $newUser->getUserId()))
						{
							// they are valid and their key is correct
							if ($_GET['activate'] == "true")
							{
								User::activateUser($_GET['userName']);
							}
							else
							{
								User::deactivateUser($_GET['userName']);
							}
							
							$statusMessage = sprintf(REGISTER_ACTIVATE, $_GET['userName']);
						}
						else
						{
							$statusMessage = REGISTER_ACTIVATE_FAIL;
						}
					}
					else
					{
						// If they are already active whats the point?
						$statusMessage = sprintf(REGISTER_ALREADY_ACTIVATED, $_GET['userName']);
					}
				}
			}
		}
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
	$page->addBodyData("User Registration: <br />");
	
	$registerForm = <<< REGISTER_FORM_DATA
	<div class="registeruser">
  		<form id="userregister" class="register" method="post" action="javascript:registerUser('$registerString', 'registerUserName', 'registerUserPassword', 'registerUserEmail');">
  		    <label id="registerUserNameLbl">UserName:
  			<input type="text" id="registerUserName" /></label>
  			<label id="registerUserPasswordLbl">Password:
  		    <input type="password" id="registerUserPassword" /></label>
  			<label id="registerUserEmailLbl">Email:
  		    <input type="text" id="registerUserEmail" /></label>
  		    <input type="submit" value="Register" />
  		</form>
	</div>
REGISTER_FORM_DATA;

	$page->addBodyData($registerForm);
}
?>