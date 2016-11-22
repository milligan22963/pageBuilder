<?php
include_once("../sitesetup.php");
include_once("../pageBuilder/xmlPage.php");
include_once("../toolbox/filetools.php");

define('MISSING', "The requested item does not exist.");
define('DB_EXISTS', "The requested database exists.");
define('TABLE_EXISTS', "The requested table exists.");

function loadExpectedDatabaseAssets($fileName, $databaseName)
{
	/* Load in the install.xml file and related information */
  $databaseAssets = array();

  $xmlDoc = new DOMDocument();

  $xmlDoc->load($fileName);

  $domList = $xmlDoc->getElementsByTagName("database");
  foreach ($domList as $domNode)
  {
  	if ($domNode->hasAttributes())
  	{
  		$dbName = $databaseName;
  		$dbScript = "none";
  		$dbVersion = "none";
      foreach ($domNode->attributes as $attr)
      {
        if ($attr->name == "name")
        {
//          $dbName = $attr->value;
        }
        if ($attr->name == "codefile")
        {
          $dbScript = $attr->value;
        }
        if ($attr->name == "version")
        {
          $dbVersion = $attr->value;
        }
      }
		$databaseAssets[$dbName] = array();
		$databaseAssets[$dbName]["tables"] = array();
		$databaseAssets[$dbName]["codefile"] = $dbScript;
		$databaseAssets[$dbName]["version"] = $dbVersion;
		 
	    /* For each database, there will be a set of tables that correspond to it */
		$tableList = $xmlDoc->getElementsByTagName("table");
		foreach ($tableList as $tableNode)
		{
	  		$tblName = "none";
	  		$tblScript = "none";
	  		$tblVersion = "none";
	      foreach ($tableNode->attributes as $attr)
	      {
	        if ($attr->name == "name")
	        {
	          $tblName = $attr->value;
	        }
	        if ($attr->name == "codefile")
	        {
	          $tblScript = $attr->value;
	        }
	        if ($attr->name == "version")
	        {
	          $tblVersion = $attr->value;
	        }
	      }
			$databaseAssets[$dbName]["tables"][$tblName] = array();
			$databaseAssets[$dbName]["tables"][$tblName]["codefile"] = $tblScript;
			$databaseAssets[$dbName]["tables"][$tblName]["version"] = $tblVersion;
		}
  	}
  }  
  return $databaseAssets;
}

function checkDatabase(& $tables)
{
  $systemObject = getSystemObject();
  
  $returnString = MISSING;

  $dbInstance = $systemObject->getDbInstance();

  if ($dbInstance != null)
  {
  	$dbName = $systemObject->getConfigurationData(SITE_DB_NAME);
    if ($dbInstance->doesDatabaseExist($dbName) == true)
    {
			$returnString = DB_EXISTS;

    	/* Load up each of the tables */
      $cdResourceId = 0;
			// load tables
			$command = "select table_name, table_rows from information_schema.tables where table_schema='$dbName';";
			$dbInstance->issueCommand($command, $cdResourceId);
			$rowResult = $dbInstance->getResult($cdResourceId);
			foreach ($rowResult as $row)
			{
				$tables[$row[0]] = $row[1];
			}

			$dbInstance->releaseResults($cdResourceId);
    }
  }
  return $returnString;
}

function checkTable($tableName)
{
}

function showCurrentSettings($echoData)
{
}

/* Create an xml response and dump it back if this is being called as the source file
 * otherwise just expose the functionality */
if (isFileLoaded(basename(__FILE__)))
{
	/* Create our xml response - default to direct display */
	/* it should look as follows:
	 * <status>
	 *   <results>
	 *     <database name="test">
	 *     	<table name="testing" records="10"/>
	 *      <table name="yours" records="1" />
	 *     </database>
	 * </status>
	 */
	$xmlPage = new XmlPageData();
	$xmlPage->setName("status");
	
	$xmlNode = new XmlDataObject();
	$xmlNode->setName("results");
	
	$tables = array();
	$dbExists = checkDatabase($tables);
	
	if ($dbExists == DB_EXISTS)
	{
		$systemObject = getSystemObject();
		$dbName = $systemObject->getConfigurationData(SITE_DB_NAME);
		$databaseNode = new XmlDataObject();
		$databaseNode->setName("database");
		$databaseNode->addAttribute("name", $dbName);
		$xmlNode->addChildObject($databaseNode);
		
		/* Now query each of the tables */
		foreach ($tables as $tableEntry => $rowsCount)
		{
			$tableNode = new XmlDataObject();
			$tableNode->setName("table");
			$tableNode->addAttribute("name", $tableEntry);
			$tableNode->addAttribute("records", $rowsCount);
			$databaseNode->addChildObject($tableNode);
		}
	}
	$xmlPage->addChildObject($xmlNode);
	
	$xmlPage->renderPage();
}
?>