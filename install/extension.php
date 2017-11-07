<?php
include_once("../sitesetup.php");
// configure our site first as we are installing
//setupSite(true);

include_once("../pageBuilder/xmlPage.php");
include_once("../toolbox/filetools.php");
include_once '../toolbox/usertools.php';


define('DB_CONNECT_FAIL', 'Unable to connect to the database.');
define('DB_CREATE_FAIL', 'Unable to create the selected table - extension.');
define('DB_SUCCESS', 'Successfully created extension table.');
define('DB_TABLE_ENTRY_FAIL', 'Unable to set default data into this table');
define('DB_EXISTS', 'The requested table (extension) already exists.  Replace it?');

define('TABLE_DESCRIPTION', 'A table for active extensions');

function testExtensionTable($replaceTable)
{
	$systemObject = getSystemObject();
	
	$dbName = $systemObject->getConfigurationData(SITE_DB_NAME);
	$dbInstance = $systemObject->getDbInstance();
	  
  $returnString = "None.";

  /* Create a database and make all good with the world */
  if ($dbInstance != null)
  {
    $dbExists = $dbInstance->selectDatabase($dbName);

    /* Ok if db exists, see if the table exists */
    if( $dbExists == true)
    {
      $tableExists = $dbInstance->doesTableExist(TABLE_NAME);
      if ($tableExists == false)
      {
        $returnString = "Could not find it";
      }
      else
      {
        $returnString = "Table exists!";
      }
    }
  }
  return $returnString;
}

function createExtensionTable($replaceTable)
{
	$systemObject = getSystemObject();
	
	$dbName = $systemObject->getConfigurationData(SITE_DB_NAME);
	$dbInstance = $systemObject->getDbInstance();
	  
  $returnString = "None.";

    /* Create a database and make all good with the world */
  if ($dbInstance != null)
  {
    $dbExists = $dbInstance->selectDatabase($dbName);

    /* Ok if db exists, see if the table exists */
    if( $dbExists == true)
    {
      $tableExists = $dbInstance->doesTableExist(TABLE_NAME);
      if (($tableExists == false) || ($replaceTable == true))
      {
        /* If the table exists and we are here then they must want to
           replace it */
        if ($tableExists == true)
        {
          $dbInstance->dropTable(TABLE_NAME);
        }

        $tableColumns = array();
        $tableColumns[0] = new DBTableColumn();
        $tableColumns[0]->setColumnName("id");
        $tableColumns[0]->setColumnType("int");
        $tableColumns[0]->setTypeLength(11);
        $tableColumns[0]->setAutoIncrement(true);
        $tableColumns[0]->setPrimaryKey(true);

        $tableColumns[1] = new DBTableColumn();
        $tableColumns[1]->setColumnName("class");
        $tableColumns[1]->setColumnType("varchar");
        $tableColumns[1]->setTypeLength(256);
        $tableColumns[1]->setAllowNull(false);
        
        $tableColumns[2] = new DBTableColumn();
        $tableColumns[2]->setColumnName("instance");
        $tableColumns[2]->setColumnType("int");
        $tableColumns[2]->setTypeLength(4);
                
        $tableColumns[3] = new DBTableColumn();
        $tableColumns[3]->setColumnName("active");
        $tableColumns[3]->setColumnType("BIT");
        $tableColumns[3]->setTypeLength(1);
        $tableColumns[3]->setAllowNull(true);
        $tableColumns[3]->setDefaultValue("b'0'");
        
        $tableColumns[4] = new DBTableColumn();
        $tableColumns[4]->setColumnName("position");
        $tableColumns[4]->setColumnType("enum('none', 'pre', 'centered', 'post')");
        $tableColumns[4]->setAllowNull(true);
        $tableColumns[4]->setDefaultValue("'none'");

        $tableColumns[5] = new DBTableColumn();
        $tableColumns[5]->setColumnName("location");
        $tableColumns[5]->setColumnType("enum('none', 'header', 'header_content', 'body', 'body_content', 'footer', 'footer_content')");
        $tableColumns[5]->setAllowNull(true);
        $tableColumns[5]->setDefaultValue("'none'");

        $tableColumns[6] = new DBTableColumn();
        $tableColumns[6]->setColumnName("time_stamp");
        $tableColumns[6]->setColumnType("TIMESTAMP");
        $tableColumns[6]->setAllowNull(false);
     
        /* Create the table, the prefix will be added by the create call */
        if ($dbInstance->createTable(TABLE_NAME, $tableColumns, TABLE_DESCRIPTION) == true)
        {
        	// Setup default data i.e. login extension
        	$extensionManager = ExtensionManager::getInstance();
        	$extensionManager->activateExtension("LoginExtension", DISPLAY_POST, BODY_WIDGET_AREA);
        	$extensionManager->activateExtension("MenuExtension", DISPLAY_PRE, BODY_WIDGET_AREA);
        	
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
  }
  else
  {
    $returnString = DB_CONNECT_FAIL;
  }

  return $returnString;
}

/* Process create request */

/* Default to not replace if the table already exists */
$replace = false;

if (isset($_GET['replace']) == true)
{
  if ($_GET['replace'] == "true")
  {
    $replace = true;
  }
}

if (isset($_GET['tableName']) == true)
{
	define('TABLE_NAME', $_GET['tableName']);
}
else
{
	define('TABLE_NAME', 'extension');
}
$tableCreateStatus = createExtensionTable($replace);
/*$tableCreateStatus = testExtensionTable($replace);*/

/* Create our xml response - default to direct display */
$xmlPage = new XmlPageData();
$xmlPage->setName('utility');

$xmlNode = new XmlDataObject();
$xmlNode->setName('results');
if ($tableCreateStatus != DB_SUCCESS)
{
  if ($tableCreateStatus == DB_EXISTS)
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
$xmlNode->setValue($tableCreateStatus);
$xmlPage->addChildObject($xmlNode);

$xmlPage->renderPage();

?>