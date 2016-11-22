<?php

define('SETTHEME_SUCCESS', "The theme: %s has been selected");
define('SETTHEME_FAILURE', "Unable to set the theme to: %s");
define('SETTHEME_LOGIN', "Must login before changing themes.");

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
	$systemObject = getSystemObject();
	
	$returnData = null;
	
	// if the option for selecting a theme is set then update the setting
	// and return an alternate rendering
	if (isset($_GET["settheme"]))
	{
		$success = false;
		
		$xmlPage = new XmlPageData();
		$xmlPage->setDirectDisplay(false);
		
		$xmlPage->setName('admin');
		
		$xmlNode = new XmlDataObject();
		$xmlNode->setName('results');
	
		$statusMessage = SETTHEME_LOGIN;
		
		$loginInstance = UserSession::getInstance();
		
		/* This will check if logged in and if so return the type of user */
		if ($loginInstance->getUserType() == USER_TYPE_ADMIN)
		{
			$newTheme = $_GET["settheme"];
			$statusMessage = sprintf(SETTHEME_FAILURE, $newTheme);
			
			// Ok they want to change the theme
			$themePath = "../" . $systemObject->getThemePath(true);
		
			if (is_dir($themePath . $newTheme))
			{
				$currentTheme = new Theme();
				$currentTheme->loadThemeFile($themePath . $newTheme . "/" . THEME_FILE_NAME);
				
				// If an error then return error
				$systemObject->setSetting("theme", $newTheme, true);
				$success = true;
				$statusMessage = sprintf(SETTHEME_SUCCESS, $newTheme);
			}
		}
		
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
	
	if ($userType == USER_TYPE_ADMIN)
	{
		/*
		 * Get the admin page data for selecting a theme
		 */
		loadScript("UIDROPPABLE", null, $page);
	
		// We are in the admin directory so need to get the baseScript url which will be our current path
		$themeSelectString = $systemObject->getBaseScriptURL() . "index.php";
		
		// Add in our javascript file
		$javaScriptFile = $systemObject->getBaseScriptURL() . "js/selecttheme.js";
		$page->addJavaScriptLink($javaScriptFile);
		
		$themePath = "../" . $systemObject->getThemePath(true);
	
		$currentThemeName = $systemObject->getSetting("theme", "default");
		
		$currentTheme = new Theme();
		$currentTheme->loadThemeFile($themePath . $currentThemeName . "/" . THEME_FILE_NAME);
		
		$page->addBodyData("Current theme: " . $currentTheme->getName() . "<br />");
	
		$themes = scandir($themePath);
		foreach ($themes as $index=>$name)
		{
			$path = $themePath . $name;		
			if (is_dir($path))
			{
				if (($name != ".") && ($name != ".."))
				{
					$themeFile = new Theme();
					$themeFile->loadThemeFile($path . "/" . THEME_FILE_NAME);
					$page->addBodyData("Theme Name: " . $themeFile->getName() . "<br />");
					$page->addBodyData("Theme Description: " . $themeFile->getDescription() . "<br />");
					
					$themeFileId = "theme_" . $themeFile->getName();
					$themeSnippet = <<<THEME_SNIPPET
	<div class="themeselection">
  		<form class="themeselect" id="$themeFileId" method="get" action="javascript:selectSiteTheme('$themeSelectString', '$name');">
    		<input type="submit" value="Select Theme" />
  		</form>
	</div>
THEME_SNIPPET;
					$page->addBodyData($themeSnippet);
				}
			}
		}
	}
	else 
	{
		// not available to non-admins
		$page->addBodyData("<div>Only administrators have the ability to access this.</div>");
	}
}
?>