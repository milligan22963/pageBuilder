<?php
include_once("../sitesetup.php");

// configure our site first as we are installing
//setupSite(true);

include_once("../pageBuilder/page.php");
include_once("../toolbox/filetools.php");
include_once("../toolbox/scriptmanager.php");
include_once 'query.php';

define('QUERY_SCRIPT', 'query.php');

$installPage = new Page();
$installPage->setPageType(DTD_TRANSITIONAL);

$systemObject = getSystemObject();

$installPage->setTitle($systemObject->getConfigurationData(SITE_TITLE));

$baseInstallArea = $systemObject->getBaseScriptURL();

$styleSheetName = $baseInstallArea . "install.css";
$installPage->addStyleSheet($styleSheetName);

/* Add in jQuery items */
loadScript("JQUERY", $baseInstallArea . "..", $installPage);

/* Add local js for the install */
$javaScriptName = $baseInstallArea . "js/install.js";
$installPage->addJavaScriptLink($javaScriptName);

$databaseName = $systemObject->getConfigurationData(SITE_DB_NAME);
$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);

/* Load what we think should be in in this system */
#$dbAssets = loadExpectedDatabaseAssets($baseInstallArea . "install.xml");
$dbAssets = loadExpectedDatabaseAssets("install.xml", $databaseName);
//var_dump($dbAssets);
$dbAssetDump = print_r($dbAssets, true);


$dbCreateString = $baseInstallArea . $dbAssets[$databaseName]["codefile"];

error_log("Starting query");

$bodyData = <<<INSTALL_BODY_DATA
<!-- div>Installing database and tables...$dbAssetDump</div -->
<div>($databaseName) Create String: $dbCreateString</div>
INSTALL_BODY_DATA;

/* before we build the page, determine what exists and what doesn't 
 * This is basically assuming only one database however the install.xml file will allow multiples
 * */
$dbArray = array();
$dbExists = checkDatabase($dbArray);
if ($dbExists == DB_EXISTS)
{
	/* The database already exists so see if any of the expected tables exist
	 * and then proceed accordingly - the passed array has all of the existing data
	 */
	$bodyData .= <<<INSTALL_BODY_DATA
	<div class="successDB">
  		<form id="database" method="get" action="javascript:createDatabase('$dbCreateString', true);">
    		<input type="submit" value="Replace Database" />
  		</form>
	</div>
INSTALL_BODY_DATA;


	/* For each existing table, show it and allow it to be replaced */
	foreach ($dbArray as $tableEntry => $rowsCount)
	{
		$bodyData .= '<!-- ' . $tableEntry . ' -->';
		$tableName =  str_replace($tablePrefix, "", $tableEntry);
		if (array_key_exists($tableName, $dbAssets[$databaseName]["tables"]))
		{
			$tableCreateString = $baseInstallArea . $dbAssets[$databaseName]["tables"][$tableName]["codefile"];
		
			$bodyData .= <<<INSTALL_BODY_DATA
	<div>Table Create String: $tableCreateString</div>
	<div class="successDB">
  		<form class="visibletable" method="get" action="javascript:createUserTable('$tableCreateString', true, '$tableName');">
    		<input type="submit" value="Replace $tableEntry Table" />
  		</form>
	</div>
INSTALL_BODY_DATA;
		}
	}
	
	/* Now we need to make all of the currently non-existing tables existing */
//	print_r($dbAssets[$databaseName]);
//	if ($dbAssets[$databaseName].length > 0)
//	{
		$tableData = "<div>Adding missing tables</div>";
		$bodyData .= $tableData;
		foreach ($dbAssets[$databaseName]["tables"] as $tableName => $tableEntry)
		{
			$bodyData .= '<!-- ' . $tableName . ' -->';
	
			/* Does this table already exist? */
			if (!array_key_exists($tablePrefix . $tableName, $dbArray))
			{
			$tableCreateString = $baseInstallArea . $tableEntry["codefile"];
		$bodyData .= <<<INSTALL_BODY_DATA
		  <form class="visibletable" method="get" action="javascript:createUserTable('$tableCreateString', false, '$tableName');">
		    <input type="submit" value="Create {$tablePrefix}$tableName Table" />
		  </form>
INSTALL_BODY_DATA;
			}
		}
//	}
}
else
{
	$bodyData .= <<<INSTALL_BODY_DATA
<div class="successdb">
  <!-- form id="database" method="get" onsubmit="ProcessDbCreateRequest('$dbCreateString');" action="javascript:ProcessDbCreateRequest('$dbCreateString');" -->
  <form id="database" method="get" action="javascript:createDatabase('$dbCreateString', false);">
    <input type="submit" value="Create Database" />
  </form>
INSTALL_BODY_DATA;
	foreach ($dbAssets[$databaseName]["tables"] as $tableName => $tableEntry)
	{
		$tableCreateString = $baseInstallArea . $tableEntry["codefile"];
	$bodyData .= <<<INSTALL_BODY_DATA
  <form class="hiddentable" method="get" action="javascript:createUserTable('$tableCreateString', false, '$tableName');">
    <input type="submit" value="Create {$tablePrefix}$tableName Table" />
  </form>
</div>
INSTALL_BODY_DATA;
	}
}

$installPage->addBodyData($bodyData);

echo $installPage->renderPage();

?>
