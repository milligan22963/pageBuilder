<?php

function getPageHeader(& $page, $themePath, $displayPosition, $callOrder)
{
	$headerData = null;
	
//	error_log("getting page header");
	if ($displayPosition == DISPLAY_PRE)
	{
		if ($callOrder == FIRST_CALL)
		{
			$pageMeta = new MetaDataObject();
			$pageMeta->setNameContent("viewport", "width=device-width, initial-scale=1.0");
		
			$page->addDisplayObject($pageMeta);
						
			//<label id="headertitle">$siteTitle</label>
			$systemObject = getSystemObject();
			$siteTitle = $systemObject->getConfigurationData(SITE_TITLE);
			$siteTagline = $systemObject->getConfigurationData(SITE_TAG_LINE);
			$headerData = <<<HEADER_DATA
	<section id="main">
	<header id="outer" class="ui-widget ui-widget-header ui-corner-all">
			<img id="headerImage" src="${themePath}images/placeholder.jpg"/>
			<label id="headertagline">$siteTagline</label>
HEADER_DATA;
		}
	}
	else if ($displayPosition == DISPLAY_POST)
	{
		if ($callOrder == SECOND_CALL)
		{
			$headerData = <<<HEADER_DATA
	</header>
	<section id="bodycontainer">
HEADER_DATA;
		}
	}
  if ($headerData != null)
  {
  	$page->addBodyData($headerData);
  }
}
?>
