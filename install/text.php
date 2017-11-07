<?php
include_once("../sitesetup.php");
// configure our site first as we are installing
//setupSite(true);

include_once("../pageBuilder/xmlPage.php");
include_once("../toolbox/filetools.php");
include_once '../toolbox/usertools.php';


define('DB_CONNECT_FAIL', 'Unable to connect to the database.');
define('DB_CREATE_FAIL', 'Unable to create the selected table - text.');
define('DB_SUCCESS', 'Successfully created text table.');
define('DB_TABLE_ENTRY_FAIL', 'Unable to set default data into this table');
define('DB_EXISTS', 'The requested table (text) already exists.  Replace it?');

define('TABLE_DESCRIPTION', 'A table for text entries for this site');

function testTextTable($replaceTable)
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

function createTextTable($replaceTable)
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
        
       	$table = new DBTable();

       	// These are all of the user's defaults
       	$table->addTableColumn("id", DB_INT, 11, 0, false, 0, true, true);
       	$table->addTableColumn("user_id", DB_INT, 11);
		$table->addTableColumn("user_key", DB_VARCHAR, 64, 0, false);
       	$table->addTableColumn("lang_id", DB_VARCHAR, 8, 0, true, "'en_US'");
		$table->addTableColumn("font_id", DB_INT, 11, 0, true);
		$table->addTableColumn("font_size", DB_INT, 4, 0, true, 12);
		$table->addTableColumn("font_decoration", DB_VARCHAR, 64, 0, true);
		$table->addTableColumn("text", DB_VARCHAR, 1024, 0, true);
        $table->addTableColumn("text_color", DB_VARCHAR, 8, 0, true, "000000"); // include RGB
        $table->addTableColumn("text_opacity", DB_DECIMAL, 5, 2, true, 100.00);

        /* create enum type for default position */
        $positionEnum = "enum(";
		$positionArray = array('topleft', 'top', 'topright', 'middleleft', 'middle', 'middleright', 'bottomleft', 'bottom', 'bottomright');
        
		$total = count($positionArray);
		$index = 1;
        foreach ($positionArray as $positionOption)
        {
        	$positionEnum .= "'" . $positionOption . "'";
        	if ($index < $total)
        	{
        		$positionEnum .= ", ";
        		$index++;
        	}
        }
        $positionEnum .= ')';
		$table->addTableColumn("position", $positionEnum, 0, 0, true, "'middle'");

		$table->addTableColumn("offset_x", DB_INT, 4, 0, true, 0);
		$table->addTableColumn("offset_y", DB_INT, 4, 0, true, 0);
        $table->addTableColumn("active", DB_BIT, 1, 0, true, "b'1'");
        $table->addTableColumn("time_stamp", DB_TIMESTAMP);
			
        $table->setTableName(TABLE_NAME);
        $table->setTableDescription(TABLE_DESCRIPTION);
	        
        /* Create the table, the prefix will be added by the create call */
        $success = $table->createTable($dbInstance);
        
        /* Create the table, the prefix will be added by the create call */
        if ($success == true)
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
	define('TABLE_NAME', 'text');
}
$tableCreateStatus = createTextTable($replace);
/*$tableCreateStatus = testTextTable($replace);*/

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