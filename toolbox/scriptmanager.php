<?php
/*
 * ScriptManager - used to manage scripts such as jQuery and jQUeryUI so the user doesn't have to deal with
 * knowing all of the bits and pieces to be loaded.
 */

include_once dirname(__FILE__) . '/../pageBuilder/page.php';
include_once dirname(__FILE__) . '/../toolbox/filetools.php';

define("SCRIPT_PATH", "scriptpath");
define("SCRIPT_DATA", "script");
define("CSS_PATH", "csspath");
define("CSS_FILE", "cssfile");
define("DEPENDENCY", "p:dependency");
define("CSS", "p:css");

/*
 * Global variable that if it doesn't exist then initialize it
 * This will map in each script path and then each script
 * Each script will have 0 or more dependencies
 * array[scriptpath] = array
 *   array[scriptpath][name] = path
 * array[script] = array
 *   array[script][shortname] = array
 *     array[script][shortname][version] = string
 *     array[script][shortname][codefile] = string
 *     array[script][shortname][pathId] = string
 *     array[script][shortname][dependencies] = array
 *     	 array[script][shortname][depenencies][id] = string
 */
$g_scriptSpecificationArray = null;

/*
 * initializeScriptData
 * 
 * param fileName - the name of the file to initialize with
 * 
 * param scriptArray - the name of the array to populate otherwise we will
 *                generate a new one
 * 
 * return array of data representing the loading script data
 */
function initializeScriptData($fileName, & $scriptArray)
{
	if ($scriptArray == null)
	{
	  $scriptArray = array();
  	  $scriptArray[SCRIPT_PATH] = array();
      $scriptArray[SCRIPT_DATA] = array();
      $scriptArray[CSS_PATH] = array();
      $scriptArray[CSS_FILE] = array();
	}
  $xmlDoc = new DOMDocument();

  $xmlDoc->load($fileName);

  $pathList = $xmlDoc->getElementsByTagName(SCRIPT_PATH);
  foreach ($pathList as $pathNode)
  {
  	$name = "none";
  	$path = "none";
  	foreach ($pathNode->attributes as $option)
      {
      	if ($option->name == "name")
        {
          $name = $option->value;
        }
        if ($option->name == "path")
        {
          $path = $option->value;
        }
        
        if ($name != "none" && $path != "none")
        {
        	$scriptArray[SCRIPT_PATH][$name] = $path;
        }
      }
  }

  $pathList = $xmlDoc->getElementsByTagName(CSS_PATH);
  foreach ($pathList as $pathNode)
  {
  	$name = "none";
  	$path = "none";
  	foreach ($pathNode->attributes as $option)
      {
      	if ($option->name == "name")
        {
          $name = $option->value;
        }
        if ($option->name == "path")
        {
          $path = $option->value;
        }
        
        if ($name != "none" && $path != "none")
        {
        	$scriptArray[CSS_PATH][$name] = $path;
        }
      }
  }
  
  $cssList = $xmlDoc->getElementsByTagName(CSS_FILE);
  foreach ($cssList as $cssNode)
  {
  	$codefile = "none";
  	$pathId = "none";
  	$name = "none";
  	  foreach ($cssNode->attributes as $option)
      {
       	if ($option->name == "codefile")
        {
          $codefile = $option->value;
        }
        if ($option->name == "pathId")
        {
          $pathId = $option->value;
        }
        if ($option->name == "name")
        {
          $name = $option->value;
        }
        
        if ($codefile != "none" && $pathId != "none" && $name != "none")
        {
        	$scriptArray[CSS_FILE][$name] = array();
        	$scriptArray[CSS_FILE][$name][CSS_FILE] = $codefile;
        	$scriptArray[CSS_FILE][$name][CSS_PATH] = $pathId;
        }
      }
  }
  
  $scriptList = $xmlDoc->getElementsByTagName(SCRIPT_DATA);
  foreach ($scriptList as $scriptNode)
  {
  	$shortname = "none";
  	$version = "none";
  	$codefile = "none";
  	$pathId = "none";
  	$conditional = "NO";
  	$flatten = false;
  	  foreach ($scriptNode->attributes as $option)
      {
      	if ($option->name == "shortname")
        {
          $shortname = $option->value;
        }
      	if ($option->name == "version")
        {
          $version = $option->value;
        }
       	if ($option->name == "codefile")
        {
          $codefile = $option->value;
        }
        if ($option->name == "pathId")
        {
          $pathId = $option->value;
        }
        if ($option->name == "conditional")
        {
        	$conditional = $option->value;
        }
        if ($option->name == "flatten")
        {
        	$flatten = $option->value == 'true' ? true : false;
        }
        if ($shortname != "none" && $pathId != "none")
        {
        	$scriptArray[SCRIPT_DATA][$shortname] = array();
        	$scriptArray[SCRIPT_DATA][$shortname]["version"] = $version;
        	$scriptArray[SCRIPT_DATA][$shortname]["codefile"] = $codefile;
           	$scriptArray[SCRIPT_DATA][$shortname]["pathId"] = $pathId;
           	$scriptArray[SCRIPT_DATA][$shortname]["conditional"] = $conditional;
           	$scriptArray[SCRIPT_DATA][$shortname]["flatten"] = $flatten;
           	$scriptArray[SCRIPT_DATA][$shortname]["dependencies"] = array();
           	$scriptArray[SCRIPT_DATA][$shortname]["css"] = array();
           	
           	if ($scriptNode->hasChildNodes())
           	{
	           	foreach ($scriptNode->childNodes as $childNode)
	           	{
	           		if ($childNode->nodeName == DEPENDENCY)
	           		{
		           		/* Each child node is a dependency */
		           		if ($childNode->hasAttributes())
		           		{
		           			foreach ($childNode->attributes as $attrOption)
		           			{
		           				if ($attrOption->name == "id")
		           				{
		           					$scriptArray[SCRIPT_DATA][$shortname]["dependencies"][$attrOption->value] = $attrOption->value;
		           				}
		           			}
		           		}
	           		}
	           		else if ($childNode->nodeName == CSS)
	           		{
		           		/* Each child node is or a css link */
		           		if ($childNode->hasAttributes())
		           		{
		           			foreach ($childNode->attributes as $attrOption)
		           			{
		           				if ($attrOption->name == "id")
		           				{
		           					$scriptArray[SCRIPT_DATA][$shortname]["css"][$attrOption->value] = $attrOption->value;
		           				}
		           			}
		           		}
	           		}
	           	}
           	}
        }
      }
  }
  return $scriptArray;
}

function setCssPath($name, $path)
{
	global $g_scriptSpecificationArray;
	
	if ($g_scriptSpecificationArray == null)
	{
		$fileName = dirname(__FILE__) . "/scripts.xml";
		$array = null;
		$g_scriptSpecificationArray = initializeScriptData($fileName, $array);
	}
	
	if (array_key_exists($name, $g_scriptSpecificationArray[CSS_PATH]))
	{
		$g_scriptSpecificationArray[CSS_PATH][$name] = $path;
	}
}

function registerUserScripts($scriptDefinition)
{
	global $g_scriptSpecificationArray;

	initializeScriptData($scriptDefinition, $g_scriptSpecificationArray);
}

/*
 * loadScript
 * 
 * param $shortName - the short name for the script to be loaded
 * param $relativePath - the relative path from the root, null if root
 * param $page - the page object that the script will be loaded into
 * 
 * return none
 */
function loadScript($shortName, $relativePath, & $page)
{
	global $g_scriptSpecificationArray;
	$success = false;
	
	$systemObject = getSystemObject();
	
	$root = $systemObject->getSiteRootURL();
	if ($relativePath != null)
	{
		$root = $relativePath;
	}
	
  /*
   * Loads the script based on its short name which maps to a set of scripts for the desired
   * functionality.
   * 
   * See scripts.xml for more details
   */
	
	if ($g_scriptSpecificationArray == null)
	{
		$fileName = dirname(__FILE__) . "/scripts.xml";
		$array = null;
		$g_scriptSpecificationArray = initializeScriptData($fileName, $array);
	}
	
	/* Once loaded, look up shortname and load all scripts required */
	if (array_key_exists($shortName, $g_scriptSpecificationArray[SCRIPT_DATA]))
	{
		$success = true;
		$scriptData = $g_scriptSpecificationArray[SCRIPT_DATA][$shortName];
		foreach ($scriptData["dependencies"] as $dependency => $value)
		{
			if (array_key_exists($value, $g_scriptSpecificationArray[SCRIPT_DATA]))
			{
				loadScript($value, $relativePath, $page);
			}
		}
		foreach ($scriptData["css"] as $cssFile => $value)
		{
			if (array_key_exists($value, $g_scriptSpecificationArray[CSS_FILE]))
			{
				$pathId = $g_scriptSpecificationArray[CSS_FILE][$value][CSS_PATH];
				$depData = $g_scriptSpecificationArray[CSS_PATH][$pathId] . "/"; 
				$depData .= $g_scriptSpecificationArray[CSS_FILE][$value][CSS_FILE];
				$linkData = $root . "/" . $depData;
				$page->addStyleSheet($linkData);
			}
		}
		
		$linkData = $root . "/" . $g_scriptSpecificationArray[SCRIPT_PATH][$scriptData["pathId"]] . "/" . $scriptData["codefile"];
		if ($scriptData['conditional'] == 'NO')
		{
			$jsl = $page->addJavaScriptLink($linkData);
			$siteFlatten = $systemObject->getConfigurationData(SITE_FLATTEN_JS);
			if ($siteFlatten == "true")
			{
				$jsl->setCanFlatten($scriptData['flatten']);
			}
			else
			{
				$jsl->setCanFlatten(false);
			}
		}
		else
		{
			switch ($scriptData['conditional'])
			{
				case 'IE':
				{
					$headerObject = new HeaderObject();
					$dataWrapper = '<!--[if lt IE 9]>' . PHP_EOL;
					$dataWrapper .= '<script type="text/javascript" src="' . $linkData . '"></script>' . PHP_EOL;
					$dataWrapper .= '<![endif]-->' . PHP_EOL;
					$headerObject->setData($dataWrapper);
					$page->addDisplayObject($headerObject);
				}
				break;
			}
		}
	}
	return $success;
}

/*
 * For testing...
 */
/*
include_once("../sitesetup.php");

$myPage = new Page();
$myPage->setPageType(DTD_TRANSITIONAL);

$titleObject = new TitleObject();
$titleObject->setData("ScriptManagerTest");
$myPage->addDisplayObject($titleObject);

loadScript("UIDRAGGABLE", null, $myPage);
loadScript("UIDROPPABLE", null, $myPage);

$bodyObject = new BodyObject();
$bodyObject->setData("<div>Hello</div>");
$myPage->addDisplayObject($bodyObject);

echo $myPage->renderPage();
*/
/*print_r($g_scriptSpecificationArray);*/
?>