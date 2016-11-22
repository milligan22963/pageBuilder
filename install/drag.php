<?php
include_once("../configure/loadconfig.php");
include_once("../pageBuilder/page.php");
include_once("../toolbox/filetools.php");
include_once("../toolbox/scriptmanager.php");
include_once 'query.php';

define('DATABASE_SCRIPT', 'database.php');
define('USER_TABLE_SCRIPT', 'users.php');
define('QUERY_SCRIPT', 'query.php');

$g_configArray = loadFile(array("database", "site"), "../configure/config.xml");

$installPage = new PageData();
$installPage->setPageType(DTD_TRANSITIONAL);

$titleObject = new TitleObject();
$titleObject->setData($g_configArray[SITE_TITLE]);

$baseInstallArea = getBaseScriptURL();

$styleSheetLink = new StyleSheetLinkObject();
$styleSheetName = $baseInstallArea . "install.css";
$styleSheetLink->setData($styleSheetName);
$installPage->addDisplayObject($styleSheetLink);

/* Add in jQuery items */
/* DROPPABLE depends on DRAGGABLE which depends on everything else.. */
loadScript("UIDROPPABLE", $baseInstallArea . "..", $installPage);

/* Add local js for the install */
$javaScriptLink = new JavaScriptLink();
$javaScriptName = $baseInstallArea . "js/install.js";
$javaScriptLink->setData($javaScriptName);
$installPage->addDisplayObject($javaScriptLink);

/* Add in calls for when the body is created */
$javaScriptSnippet = new JavaScriptObject();
$javaScriptData = <<<JAVA_SCRIPT_SNIPPET
var databaseArray = new Array();
var tableArray = new Array();
$(function() {
		$( "#source" ).draggable();
		$("#test").draggable();
		$( "#target" ).droppable({
			drop: function( event, ui ) {
				var nameValue = ui.draggable.attr("name");
				if (ui.draggable.hasClass("table"))
				{
				  ui.draggable.draggable("disable");
				  ui.draggable.css("visibility", "hidden");
				  var testString = "<p>" + nameValue + "</p>";
				  $(this).find("#tbltarget").add("#source").html(testString);
/*				  $(this).find("#tbltarget").html(nameValue);*/
				  tableArray.push(nameValue);
				}
				else if (ui.draggable.hasClass("database"))
				{
				  $(this).find("#dbtarget").html(nameValue);
				  databaseArray.push(nameValue);
				}
			}
		});
	});
JAVA_SCRIPT_SNIPPET;
$javaScriptSnippet->addData($javaScriptData);
$installPage->addDisplayObject($javaScriptSnippet);

/* Need to setup body to add in events to be done on doc creation etc */
$bodyObject = new BodyObject();

$dbCreateString = $baseInstallArea . DATABASE_SCRIPT;
$userTableString = $baseInstallArea . USER_TABLE_SCRIPT;

$bodyData = <<<INSTALL_BODY_DATA
<div id="target">
  <div id="dbtarget">
  $baseInstallArea
  </div>
  <div id="tbltarget">
  </div>
<p class="ptarget" >Databases</p><p class="ptarget">Tables</p>
<form id="targetForm" method="get" action="javascript:alert(databaseArray);">
  <input type="submit" value="DoIt" />
</form>
</div>

<div id="source" class="table" name="users">
	<p>Drag me to my target</p>
</div>
<div id="test" class="database" name="dmc">
  <p>Drag me for no satisfaction</p>
</div>

INSTALL_BODY_DATA;

/* before we build the page, determine what exists and what doesn't */
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
	$bodyData .= <<<INSTALL_BODY_DATA
	<div class="successDB">
  		<form id="usertableform" method="get" action="javascript:createUserTable('$userTableString');">
    		<input type="submit" value="Replace $tableEntry Table" />
  		</form>
	</div>
INSTALL_BODY_DATA;
	}
}
else
{
$bodyData .= <<<INSTALL_BODY_DATA
<div class="successdb">
  <!-- form id="database" method="get" onsubmit="ProcessDbCreateRequest('$dbCreateString');" action="javascript:ProcessDbCreateRequest('$dbCreateString');" -->
  <form id="database" method="get" action="javascript:createDatabase('$dbCreateString', false);">
    <input type="submit" value="Create Database" />
  </form>
  <form id="usertableform" method="get" action="javascript:createUserTable('$userTableString');">
    <input type="submit" value="Create User Table" />
  </form>
</div>
INSTALL_BODY_DATA;
}
$bodyObject->setData($bodyData);

$installPage->addDisplayObject($titleObject);
$installPage->addDisplayObject($bodyObject);

echo $installPage->renderPage();

?>
