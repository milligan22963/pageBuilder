<?php
// Use base directory here - later in included files, we can use
// the systemObject->getBaseSystemDir().
$baseDir = dirname(__FILE__);
include_once $baseDir . '/sitesetup.php'; // this needs to be first
include_once $baseDir . '/pageBuilder/page.php';
include_once $baseDir . '/toolbox/scriptmanager.php';
include_once $baseDir . '/toolbox/tools.php';
include_once $baseDir . '/pageBuilder/theme.php';
include_once $baseDir . '/helpers.php';
include_once $baseDir . '/usersession.php';

$systemObject = System::getInstance();

// Allow the system to know the context of the access
$systemObject->setConfigurationData(PAGE_DOMAIN, PAGE_DOMAIN_USER);

processUserLogin();



/*
 * A page will consist of
 * The title
 * The header (widgets?)
 * The body (widgets?)
 * The footer (widgets?)
 * 
 * menu's are another extension that is provided and can appear anywhere on the page
 */
/* The extension manager is loaded and ready to run at this point */
$extensionManager = ExtensionManager::getInstance();

/*
 * Start off and see which page is being requested
 * 
 */
$userOption = null;
$extensionName = null;
$extensionOption = null;
$paramArray = null;

if (isset($_GET['option']))
{
	$userOption = $_GET['option'];
}
if (isset($_POST['option']))
{
	$userOption = $_POST['option'];
}

if ($userOption == null)
{
	$userOption = $systemObject->getConfigurationData(SITE_CURRENT_PAGE_NAME);
}
else
{
	$systemObject->setConfigurationData(SITE_CURRENT_PAGE_NAME, $userOption);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
	$paramArray = $_POST;
	if (isset($_POST['extensionOption']))
	{
		$extensionOption = $_POST['extensionOption'];
	}
	
	if (isset($_POST['extension']))
	{
		$extensionName = $_POST['extension'];
	}
}
else if ($_SERVER['REQUEST_METHOD'] === 'GET')
{
	$paramArray = $_GET;
	
	if (isset($_GET['extensionOption']))
	{
		$extensionOption = $_GET['extensionOption'];
	}
	if (isset($_GET['extension']))
	{
		$extensionName = $_GET['extension'];
	}
}

$systemObject->setConfigurationData(SITE_EXTENSION_OPTION, $extensionOption);
$systemObject->setConfigurationData(SITE_ACTIVE_EXTENSION, $extensionName);

$extensionData = null;
if (($extensionName != null) && ($extensionOption != null))
{
//	error_log("Processing extension command: " . $extensionName);
	$extensionData = $extensionManager->processExtensionCommands($extensionName, $extensionOption, $paramArray);
}

if ($extensionData != null)
{
//    header("Content-Type: application/xhtml+xml;charset=iso-8859-1");
//error_log($extensionData);
	echo $extensionData;
}
else
{
	/* Create page to display */
	$indexPage = new Page();
	
	/* This will set the title for the page */
	$indexPage->setTitle($systemObject->getConfigurationData(SITE_TITLE));
	
	/* This will add in the base site stylesheet */
	$indexPage->addStyleSheet($systemObject->getSiteRootURL() . "/" . $systemObject->getConfigurationData(SITE_STYLE_SHEET));
	
	// By default JQUERY will be available to all standard pages/extensions/themes
	loadScript("JQUERY", null, $indexPage);
	
	/*
	 * is there a theme to use? 
	 * if not then we will use the default
	 * 
	 */
	$theme = $systemObject->getSetting("theme", "default");
	
	$themePath = $systemObject->getThemePath(false) . $theme . "/";
	$relativeThemePath = $systemObject->getThemePath(true) . $theme . "/";
	
	$themeFile = new Theme();
	$themeFile->loadThemeFile($relativeThemePath . THEME_FILE_NAME);
	
	// Pull theme data
	$styleSheets = $themeFile->getStyleSheets();
	foreach ($styleSheets as $sheet=>$active)
	{
		$indexPage->addStyleSheet($themePath . $sheet);
	}
		
	
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
	loadScript("UICORE", null, $indexPage);
	
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
		 * User content goes between the PRE and POST body
		 */
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, HEADER_WIDGET_AREA);
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, HEADER_CONTENT_AREA);

		displayHeader($indexPage, $themePath , $relativeThemePath . $themeHeader, DISPLAY_POST);
	}
	
	$extensionManager->displayExtensions($indexPage, DISPLAY_POST, HEADER_WIDGET_AREA);

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
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, BODY_WIDGET_AREA);
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, BODY_CONTENT_AREA);
		
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
		 * User content goes between the PRE and POST body
		 */
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, FOOTER_WIDGET_AREA);
		$extensionManager->displayExtensions($indexPage, DISPLAY_INSIDE, FOOTER_CONTENT_AREA);

		displayFooter($indexPage, $themePath, $relativeThemePath . $themeFooter, DISPLAY_POST);
	}
		
	if ($themeFooterWidget != null)
	{
		displayFooterWidget($indexPage, $themePath, $relativeThemePath . $themeFooterWidget, DISPLAY_POST);
	}
	
	
	/*
	 * We are now at the end of the road
	 */
	$extensionManager->postDisplay();
	
	/* Render page for display */
	echo $indexPage->renderPage();
}
?>