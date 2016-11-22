<?php

// Since I know that admin is one level down, this will work.  Need a better solution
// One might be to redirect from the root into the admin directory
$baseSiteDir = dirname(dirname(__FILE__));

include_once $baseSiteDir . '/sitesetup.php';
include_once $baseSiteDir . '/pageBuilder/page.php';
include_once $baseSiteDir . '/toolbox/scriptmanager.php';
include_once $baseSiteDir . '/toolbox/tools.php';
include_once $baseSiteDir . '/pageBuilder/theme.php';
include_once $baseSiteDir . '/helpers.php';
include_once $baseSiteDir . '/usersession.php';
include_once 'admin.php';

$paramArray = null;

$logSystem = LogToFile::getInstance();
$logSystem->setLogLevel(LOG_SYSTEM_ERROR);

$systemObject = System::getInstance();

// Allow the system to know the context of the access
$systemObject->setConfigurationData(PAGE_DOMAIN, PAGE_DOMAIN_ADMIN);

$extensionManager = ExtensionManager::getInstance();

/*
 * By default this page will handle the login via calling processUserLogin under usersession.php
 * however the end user will be required to handle what needs a log in and what security type
 */
processUserLogin();


/*
 * Load option set for the admin page - all admin functions are extensible via the admin.xml file
 */
$g_adminOptionArray = loadAdminOptions(dirname(__FILE__) . "/admin.xml");

$alternateRendering = null;

/*
 * Start off and see which page is being requested under admin
 * then if there is an alternate rendering return that opposed
 * to creating a new page
 * 
 */
$userOption = null;

if (isset($_GET['option']))
{
	$userOption = $_GET['option'];
}
if (isset($_POST['option']))
{
	$userOption = $_POST['option'];
}

if ($userOption != null)
{	
	if (array_key_exists($userOption, $g_adminOptionArray))
	{
		$includeFile = $g_adminOptionArray[$userOption];
		include_once "${includeFile}";
	}
}

/*
 * This is the generic function that will process options for
 * each admin page
 */
if (function_exists("processAdminOptions"))
{
	$alternateRendering = processAdminOptions($indexPage);
	$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Alternate rendering:' . $alternateRendering . PHP_EOL);
}

/*
 * if no admin commands then pass on to the extensions for any of their commands
 */
if ($alternateRendering == null)
{
	$extensionName = null;
	$extensionOption = null;
	
	if (isset($_GET['extensionOption']))
	{
		$paramArray = $_GET;
		$extensionOption = $_GET['extensionOption'];
	}
	if (isset($_POST['extensionOption']))
	{
		$paramArray = $_POST;
		$extensionOption = $_POST['extensionOption'];
	}
	
	if (isset($_GET['extension']))
	{
		$extensionName = $_GET['extension'];
	}
	if (isset($_POST['extension']))
	{
		$extensionName = $_POST['extension'];
	}
	
	$systemObject->setConfigurationData(SITE_EXTENSION_OPTION, $extensionOption);
	$systemObject->setConfigurationData(SITE_ACTIVE_EXTENSION, $extensionName);
	
	if (($extensionName != null) && ($extensionOption != null))
	{
		$alternateRendering = $extensionManager->processExtensionCommands($extensionName, $extensionOption, $paramArray);
	}
}

/*
 * If alternate rendering is null
 * we will continue to create the page
 * otherwise we will return the alternate rendering
 */

if ($alternateRendering == null)
{
	/*
	 * A page will consist of
	 * The title
	 * The header (widgets?)
	 * The body (widgets?)
	 * The footer (widgets?)
	 * 
	 * menu's are another extension that is provided and can appear anywhere on the page
	 */
	
	/* Create page to display */
	$indexPage = new Page();
	
	/* This is an admin page so mark it as such */
	$indexPage->setPageDomain(PAGE_DOMAIN_ADMIN);
	
	/* This will set the title for the page */
	$indexPage->setTitle($systemObject->getConfigurationData(SITE_TITLE));
	
	/* This will add in the base site stylesheet */
	$indexPage->addStyleSheet($systemObject->getSiteRootURL() . "/" . $systemObject->getConfigurationData(SITE_STYLE_SHEET));
	
	// By default JQUERY will be available to all standard pages/extensions/themes
	loadScript("JQUERY", null, $indexPage);
	loadScript("COMMON", null, $indexPage);
	loadScript("TOOLS", null, $indexPage);
		
	/*
	 * Right now there is only one theme for the administration side
	 * Since this is only used primarily by administrators, I will leave
	 * it as is however the user can update the "theme" path as they see fit
	 * In the future, themes could come in two flavors, general and admin, with the
	 * site setting for theme applying to both
	 */
	$themePath = "theme/";
	
	$themeFile = new Theme();
	$themeFile->loadThemeFile($themePath . THEME_FILE_NAME);
	
	$themeLinkPath = $systemObject->getSiteRootURL() . "/admin/theme/";
	
	// Pull theme data
	$styleSheets = $themeFile->getStyleSheets();
	foreach ($styleSheets as $sheet=>$active)
	{
		$indexPage->addStyleSheet($themeLinkPath . $sheet);
	}
	
	$relativeThemePath = $themePath;// . "/";
	
	/*
	 * Create opening DIV for format
	 * id="main"
	 */
	
	$indexPage->addBodyData("<div id=\"main\">");
	
	
	/*
	 * Load theme function file if available
	 */
	$functionFile = $themeFile->getFunctionFile();
	if ($functionFile != null)
	{
		includeFunctionFile($relativeThemePath . $functionFile);

		if (function_exists("loadThemeOverrides") == true)
		{
			loadThemeOverrides();
		}
	}

	/*
	 * Load jQuery UI after theme has been applied
	 */
	loadScript("UIDIALOG",null, $indexPage);
	
	/*
	 * We are now ready to start display the page content - prep all exetensions
	 */
	$extensionManager->preDisplay($paramArray);
	
	/*
	 * Display header
	 *   widgets? call headerWidget();
	 */
	
	/* We check for a widget, if there is and its PRE then do it otherwise if there is
	 *  and its post, then do it later
	 */
	$themeHeaderWidget = $themeFile->getHeaderWidgetFile();
	if ($themeHeaderWidget != null)
	{
		displayHeaderWidget($indexPage, $themePath, $relativeThemePath . $themeHeaderWidget, DISPLAY_PRE);
	}
	
	$themeHeader = $themeFile->getHeaderFile();
	if ($themeHeader != null)
	{
		displayHeader($indexPage, $themePath , $relativeThemePath . $themeHeader, DISPLAY_PRE);
		
		/*
		 * User content goes between the PRE and POST header
		 */
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, HEADER_CONTENT_AREA);
		
		displayHeader($indexPage, $themePath , $relativeThemePath . $themeHeader, DISPLAY_POST);
	}
	
	if ($themeHeaderWidget != null)
	{
		displayHeaderWidget($indexPage, $themePath, $relativeThemePath . $themeHeaderWidget, DISPLAY_POST);
	}
		
	/*
	 * Display Body
	 *   widgets? call bodyWidget();
	 */
	$themeBodyWidget = $themeFile->getBodyWidgetFile();
	if ($themeBodyWidget != null)
	{
		displayBodyWidget($indexPage, $themePath, $relativeThemePath . $themeBodyWidget, DISPLAY_PRE);
	}
	
	$themeBody = $themeFile->getBodyFile();
	if ($themeBody != null)
	{
		displayBody($indexPage, $themePath, $relativeThemePath . $themeBody, DISPLAY_PRE);
		
		/*
		 * User content goes between the PRE and POST body
		 */
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, BODY_CONTENT_AREA);
		
		/*
		 * This is the generic function that each admin page will implement
		 */
		if (function_exists("getAdminBodyData"))
		{
			getAdminBodyData($indexPage, UserSession::getInstance()->getUserType());
		}
		
		displayBody($indexPage, $themePath, $relativeThemePath . $themeBody, DISPLAY_POST);
	}
	
	if ($themeBodyWidget != null)
	{
		displayBodyWidget($indexPage, $themePath, $relativeThemePath . $themeBodyWidget, DISPLAY_POST);
	}
	
	
	/*
	 * Display footer
	 *  widgets? call footerWidget();
	 */
	$themeFooterWidget = $themeFile->getFooterWidgetFile();
	if ($themeFooterWidget != null)
	{
		displayFooterWidget($indexPage, $themePath, $relativeThemePath . $themeFooterWidget, DISPLAY_PRE);
	}
	
	$themeFooter = $themeFile->getFooterFile();
	if ($themeFooter != null)
	{
		displayFooter($indexPage, $themePath, $relativeThemePath . $themeFooter, DISPLAY_PRE);
	
		/*
		 * User content goes between the PRE and POST footer
		 */
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, FOOTER_CONTENT_AREA);
		
		displayFooter($indexPage, $themePath, $relativeThemePath . $themeFooter, DISPLAY_POST);
	}
	 
	if ($themeFooterWidget != null)
	{
		displayFooterWidget($indexPage, $themePath, $relativeThemePath . $themeFooterWidget, DISPLAY_POST);
	}
	
	/*
	 * Create closing DIV for format
	 * id=main
	 */
	
	$indexPage->addBodyData("</div>");
	
	/*
	 * We are now at the end of the road
	 */
	$extensionManager->postDisplay();
	
	/* Render page for display */
	echo $indexPage->renderPage();
}
else
{
	echo $alternateRendering;
}
?>