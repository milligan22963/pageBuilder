<?php

/*
 * ManageExtensions
 * 
 * This module is used to manage which extensions are active and which are not.  It can also be used
 * to add/remove extensions.
 * 
 */
define('MANAGE_EXT_ACTIVATE_SUCCESS', "The extension %s has been activated.");
define('MANAGE_EXT_DEACTIVATE_SUCCESS', "The extension %s has been deactivated.");
define('MANAGE_EXT_UPDATE_SUCCESS', "The extension %s has been updated.");
define('MANAGE_EXT_DELETE_SUCCESS', "The extension %s has been deleted.");
define('MANAGE_EXT_USER_FAILURE', "You do not have the required permissions for this option");

define('MANAGE_EXT_ACTIVATE_COMMAND', 'Activate');
define('MANAGE_EXT_DEACTIVATE_COMMAND', 'Deactivate');
define('MANAGE_EXT_UPDATE_COMMAND', 'Update');
define('MANAGE_EXT_DELETE_COMMAND', 'Delete');

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
	
	$logSystem = LogToFile::getInstance();
	$logSystem->logInformation(LOG_SYSTEM_TRACE, 'processAdminOptions: ' . __LINE__ . PHP_EOL);
	
	/*
	 *  if the user has admin priviledges then allow them to update which extension is active
	 *  and which one isn't along with whether it needs deleting or the like
	 *  
	 */
	if (isset($_POST['extension']) && isset($_POST['action']))
	{
		$statusMessage = $_POST['extension'] . ":" . $_POST['action'];
		$generateResponse = true;
		
		/*
		 * Ensure that the user has the correct permissions and that the specified extension is actually valid
		 */
		
		$userInstance = UserSession::getInstance();
		if ($userInstance->getUserType() == USER_TYPE_ADMIN)
		{
			$extManager = ExtensionManager::getInstance();
			
			switch ($_POST['action'])
			{
				case MANAGE_EXT_ACTIVATE_COMMAND:
					$extManager->activateExtension($_POST['extension'], $_POST['position'], $_POST['location']);
					$statusMessage = sprintf(MANAGE_EXT_ACTIVATE_SUCCESS, $_POST['extension']);
					break;
				case MANAGE_EXT_DEACTIVATE_COMMAND:
					$extManager->deactivateExtension($_POST['extension']);
					$statusMessage = sprintf(MANAGE_EXT_DEACTIVATE_SUCCESS, $_POST['extension']);
					break;
				case MANAGE_EXT_UPDATE_COMMAND:
					$extManager->updateExtension($_POST['extension']);
					$statusMessage = sprintf(MANAGE_EXT_UPDATE_SUCCESS, $_POST['extension']);
					break;
				case MANAGE_EXT_DELETE_COMMAND:
					$extManager->deleteExtension($_POST['extension']);
					$statusMessage = sprintf(MANAGE_EXT_DELETE_SUCCESS, $_POST['extension']);
					break;
			}
			$success = true;
		}
		else
		{
			$logSystem->logInformation(LOG_SYSTEM_ERROR, 'User Name:' . $userInstance->getUserName() . PHP_EOL);
			$logSystem->logInformation(LOG_SYSTEM_ERROR, 'User Type:' . $userInstance->getUserType() . PHP_EOL);
			
			$statusMessage = MANAGE_EXT_USER_FAILURE;
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
 * @param - the current user type that is logged in if any
 * 
 * @return none 
 */
function getAdminBodyData(& $page, $userType)
{
	$systemObject = getSystemObject();
	
	if ($userType == USER_TYPE_ADMIN)
	{
		$logSystem = LogToFile::getInstance();
		$logSystem->logInformation(LOG_SYSTEM_TRACE, 'getAdminBodyData: ' . __LINE__ . PHP_EOL);
		/*
		 * Get the admin page data for managing extensions
		 */
		
		/*
		 * Load the UIDROPPABLE script which includes the rest of jQuery and jUi that we need
		 */
		loadScript("UIDROPPABLE", null, $page);
		
		// We are in the admin directory so need to get the baseScript url which will be our current path
		$manageExtString = $systemObject->getBaseScriptURL() . "index.php";
		$logSystem->logInformation(LOG_SYSTEM_TRACE, 'manageExtString: ' . $manageExtString . __LINE__ . PHP_EOL);
		
		
		// Add in our javascript file
		$javaScriptFile = $systemObject->getBaseScriptURL() . "js/manageext.js";
		$page->addJavaScriptLink($javaScriptFile);
		
		$page->addBodyData("Inactive Extensions: <br />");
		
		$extensionInstance = ExtensionManager::getInstance();
		/*
		 * This if for managing extensions
		 */
		$inactiveExtensions = $extensionInstance->getExtensions(EXTENSION_INACTIVE);
		
		foreach ($inactiveExtensions as $name=>$object)
		{
			// Need to walk through each extension and show which ones are inactive and which ones are active
			// allow the user to activate and de-activate each one
			$manageForm = <<< MANAGE_FORM_DATA
		<div class="manageext">
	  		<form id="${name}" class="manage" method="post" action="javascript:manageExtension('$manageExtString', '${name}', 'extensionName', 'activityType', 'position', 'location');">
	  		    <label id="mngExtNameLbl">Extension: ${name}</label>
	  		    <input id="extensionName" type="hidden" value="${name}"/>
	  		    <select name="activityType">
	  		      <option value="Activate" selected="selected">Activate</option>
	  		      <option value="Update">Update</option>
	  		      <option value="Delete">Delete</option>
	  		    </select>
	  		    <select name="position">
	  		      <option value="pre">Before</option>
	  		      <option value="centered">Inside</option>
	  		      <option value="post" selected="selected">After</option>
	  		    </select>
	  		    <select name="location">
	  		      <option value="header">Header</option>
	  		      <option value="header_content">Header Content</option>
	  		      <option value="body" selected="selected">Body</option>
	  		      <option value="body_content">Body Content</option>
	  		      <option value="footer">Footer</option>
	  		      <option value="footer_content">Footer Content</option>
	  		    </select>
	  		    <input type="submit" value="Submit" />
	  		</form>
		</div>
MANAGE_FORM_DATA;

			$page->addBodyData($manageForm);
		}

		$page->addBodyData("Active Extensions: <br />");
		$activeExtensions = $extensionInstance->getExtensions(EXTENSION_ACTIVE);
		foreach ($activeExtensions as $name=>$object)
		{			
			// Need to walk through each extension and show which ones are inactive and which ones are active
			// allow the user to activate and de-activate each one
			$manageForm = <<< MANAGE_FORM_DATA
	<div class="manageext">
  		<form id="${name}" class="manage" method="post" action="javascript:manageExtension('$manageExtString', '${name}', 'extensionName', 'activityType', 'position', 'location');">
  		    <label id="mngExtNameLbl">Extension: ${name}</label>
  		    <input id="extensionName" type="hidden" value="${name}"/>
  		    <select name="activityType">
  		      <option value="Deactivate" selected="selected">Deactivate</option>
  		      <option value="Update">Update</option>
  		      <option value="Delete">Delete</option>
  		    </select>
  		    <input id="position" type="hidden" value="post"/>
  		    <input id="location" type="hidden" value="none"/>
  		    <input type="submit" value="Submit" />
  		</form>
	</div>
MANAGE_FORM_DATA;

			$page->addBodyData($manageForm);
		}
	}
	else 
	{
		// not available to non-admins
		$page->addBodyData("<div>Only administrators have the ability to access this.</div>");
	}
}
?>