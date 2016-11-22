<?php
include_once '../sitesetup.php';
// configure our site first as we are installing
//setupSite(true);

include_once("../pageBuilder/xmlPage.php");
include_once("../toolbox/filetools.php");

define('DB_CONNECT_FAIL', 'Unable to connect to the database.');
define('DB_CREATE_FAIL', 'Unable to create the selected database.');
define('DB_SUCCESS', 'Successfully created user database.');
define('DB_EXISTS', 'The requested database already exists.  Replace it?');

function createUserDatabase($replace)
{
	$systemObject = getSystemObject();
	
  $returnString = "None.";

  $dbInstance = $systemObject->getDbInstance();
  if ($dbInstance == null)
  {
  	error_log("DB is null!");
	$dbName = $systemObject->getConfigurationData(SITE_DB_NAME);
	error_log("db_name: " . $dbName);
  }

  /* Create a database and make all good with the world */
  if ($dbInstance != null)
  {
	$dbName = $systemObject->getConfigurationData(SITE_DB_NAME);
    $dbExists = $dbInstance->doesDatabaseExist($dbName);

    /* If the db doesn't exist or we are ok replacing it, then proceed */
    if (($dbExists == false) || ($replace == true))
    {
      /* If we are here, wack it if it exists */
      if ($dbExists == true)
      {
        $dbInstance->dropDatabase($dbName);
      }

      if ($dbInstance->createDatabase($dbName) == true)
      {
        $returnString = DB_SUCCESS;
      }
      else
      {
        $returnString = DB_CREATE_FAIL;
      }
    }
    else
    {
      $returnString = DB_EXISTS;
    }
  }
  else
  {
    $returnString = DB_CONNECT_FAIL;
  }

  return $returnString;
}

/* Create an xml response and dump it back */
/* We should be able to create a form to get the data from the user and pass it in
   then save the settings accordingly */

/* Default to not replace if the database already exists */
$replace = false;

if (!empty($_GET['replace']))
{
  if ($_GET['replace'] == "true")
  {
    $replace = true;
  }
}


$dbCreateStatus = createUserDatabase($replace);

/* Create our xml response - default to direct display */ 
$xmlPage = new XmlPageData();
$xmlPage->setName('utility');

$xmlNode = new XmlDataObject();
$xmlNode->setName('results');
if ($dbCreateStatus != DB_SUCCESS)
{
  if ($dbCreateStatus == DB_EXISTS)
  {
    $xmlNode->setValue('-1');
  }
  else
  {
    $xmlNode->setValue('0');
  }
}
else
{
  $xmlNode->setValue('1');
}
$xmlPage->addChildObject($xmlNode);

$xmlNode = new XmlDataObject();
$xmlNode->setName('returnString');
$xmlNode->setValue($dbCreateStatus);
$xmlPage->addChildObject($xmlNode);

$xmlPage->renderPage();

?>
