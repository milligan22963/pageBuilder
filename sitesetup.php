<?php
date_default_timezone_set('America/New_York');

$baseDir = dirname(__FILE__);
include_once $baseDir . '/configure/loadconfig.php';
include_once $baseDir . '/configure/configxml.php';
include_once $baseDir . '/database/defaultconfig.php';
include_once $baseDir . '/database/mysqli_access.php';
include_once $baseDir . '/database/psgresql_access.php';
include_once $baseDir . '/toolbox/filetools.php';
include_once $baseDir . '/system.php';
include_once $baseDir . '/pageBuilder/extension.php'; /* Class for managing extensions */

define('SITE_CONTENT_RELATIVE_PATH', 'SITE_CONTENT_RELATIVE_PATH');
define('SITE_USER_CONTENT_RELATIVE_PATH', 'SITE_USER_CONTENT_RELATIVE_PATH');
define('SITE_THEME_RELATIVE_PATH', 'SITE_THEME_RELATIVE_PATH');
define('SITE_EXTENSION_RELATIVE_PATH', 'SITE_EXTENSION_RELATIVE_PATH');
define('SITE_ADMIN_RELATIVE_PATH', 'SITE_ADMIN_RELATIVE_PATH');

/* for themes and extensions */
define('DISPLAY_PRE', 'pre');
define('DISPLAY_POST', 'post');
define('DISPLAY_INSIDE', 'centered');

/* For extensions */
define('FIRST_CALL', 0);
define('SECOND_CALL', 1);
define('NO_WIDGET_AREA', 'none');
define('HEADER_WIDGET_AREA', 'header');
define('HEADER_CONTENT_AREA', 'header_content');
define('BODY_WIDGET_AREA', 'body');
define('BODY_CONTENT_AREA', 'body_content');
define('FOOTER_WIDGET_AREA', 'footer');
define('FOOTER_CONTENT_AREA', 'footer_content');

function installErrorHandler()
{
	error_reporting(E_ALL); 
//   $old_error_handler = set_error_handler("userErrorHandler");
}

function userErrorHandler ($errno, $errmsg, $filename, $linenum,  $vars) 
{
     $time=date("d M Y H:i:s"); 
     // Get the error type from the error number 
     $errortype = array (1    => "Error",
                         2    => "Warning",
                         4    => "Parsing Error",
                         8    => "Notice",
                         16   => "Core Error",
                         32   => "Core Warning",
                         64   => "Compile Error",
                         128  => "Compile Warning",
                         256  => "User Error",
                         512  => "User Warning",
                         1024 => "User Notice");
      $errlevel=$errortype[$errno];
            
      $errfile=fopen("errors.csv","a"); 
      fputs($errfile,"\"$time\",\"$filename . ':' . $linenum\",\"($errlevel) $errmsg\"\r\n"); 
      fclose($errfile); 
      
      return false;
}

/*
 * setupSite
 * 
 * loads configuration and settings for the user and creates a db object that can be used
 * 
 * param none
 * 
 * return mysqlDBObject
 */
function setupSite($newInstall)
{
//	installErrorHandler();
	$systemObject = System::getInstance();
		
//	$configFile = dirname(__FILE__) . "/configure/config.xml";
//	$configArray = loadFile(array("database", "site"), $configFile);

	$configData = getConfigData();
	$configArray = loadXmlData(array("database", "site"), $configData);
	
	$systemObject->setConfigurationArray($configArray);

#print_r($configArray);
        if ($configArray[DB_SYSTEM] == POSTGRESQL)
        {
	        $dbInstance = new psgresqlDBObject();
        }
        else
        {
    	    $dbInstance = new mysqliDBObject();
        }

    if ($dbInstance != null)
    {
      $dbInstance->setCredentials($configArray[DB_USER_NAME], $configArray[DB_PASSWORD]);
      $dbInstance->setHostName($configArray[DB_HOST_NAME]);
      $dbInstance->setTablePrefix($configArray[SITE_TABLE_PREFIX]);

      if ($dbInstance->selectDatabase($configArray[SITE_DB_NAME]) != true)
      {
      	// This is an error if the system is live.  Not if it hasn't been installed yet
      	if ($newInstall == false)
      	{
	        $dbInstance = null;
	        error_log("Failed to select database.");
	        die(1);
      	}
/*      	else
      	{
    		$systemObject->setDbInstance($dbInstance); // we will set this instance for creating purposes
      		return; // no database and newInstall so thats ok.
      	}*/
      }
    }
    else 
    {
    	error_log("Failed to create database instance!");
    	die(1);
    }
    $systemObject->setDbInstance($dbInstance);
    
    /* Setup system data */
	$basePath = $systemObject->getSiteRootURL() . "/";
	    
    $systemData = array();
    $contentPath = $configArray[SITE_CONTENT_PATH];
    $extensionPath = $configArray[SITE_EXTENSION_PATH];
    $themePath = $configArray[SITE_THEME_PATH];
    $adminPath = $configArray[SITE_ADMIN_PATH];
    $userContentPath = $configArray[SITE_USER_CONTENT_PATH];
    
    $systemData[SITE_CONTENT_PATH] = $basePath . $contentPath . "/";
    $systemData[SITE_CONTENT_RELATIVE_PATH] = $contentPath . "/";
    $systemData[SITE_THEME_PATH] = $basePath . $contentPath . "/" . $configArray[SITE_THEME_PATH] . "/";
    $systemData[SITE_THEME_RELATIVE_PATH] = $systemData[SITE_CONTENT_RELATIVE_PATH] . $themePath . "/";
    $systemData[SITE_USER_CONTENT_PATH] = $basePath . $contentPath . "/" . $configArray[SITE_USER_CONTENT_PATH] . "/";
    $systemData[SITE_USER_CONTENT_RELATIVE_PATH] = $systemData[SITE_CONTENT_RELATIVE_PATH] . $userContentPath . "/";
    $systemData[SITE_EXTENSION_PATH] = $basePath . $contentPath . "/" . $extensionPath . "/";
    $systemData[SITE_EXTENSION_RELATIVE_PATH] = $systemData[SITE_CONTENT_RELATIVE_PATH] . $extensionPath . "/";
    $systemData[SITE_ADMIN_PATH] = $basePath . $adminPath . "/";
    $systemData[SITE_ADMIN_RELATIVE_PATH] = $adminPath . "/";
    
    $systemObject->setSystemDataArray($systemData);
    
    /* Database is setup, load the settings */
    $systemObject->loadSettings();
    
    /* Now initialize the extension system */
    $extensionManager = ExtensionManager::getInstance();
	    
//    error_log("Loading: " . dirname(__FILE__) . "/" . $systemData[SITE_EXTENSION_RELATIVE_PATH]);
	$extensionManager->loadExtensions(dirname(__FILE__) . "/" . $systemData[SITE_EXTENSION_RELATIVE_PATH]);

	return $dbInstance;
}

function getSystemObject()
{	
	return System::getInstance();
}

class SiteSetup
{
	private static $m_instance = null;
	
	/*
	 * PHP 4 constructor
	 */
	private function SiteSetup()
	{
		setupSite(true);
	}
	
	/*
	 * PHP 5 constructor
	 */
	private function __construct()
	{
		$this->SiteSetup();
	}
	
	public static function getInstance()
	{
		if (self::$m_instance == null)
		{
			self::$m_instance = new SiteSetup();
		}
		
		return self::$m_instance;
	}
}
$siteSetup = SiteSetup::getInstance();
//setupSite(true);
?>
