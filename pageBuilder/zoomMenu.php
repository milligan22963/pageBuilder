<?php
/*
 * Created on Jun 18, 2007
 *
 * Author: 	daniel
 * File:   	zoomMenu.php
 * Project: pageBuilder
 * 
 * Purpose:
 * 		To create a menu that zooms the input focus i.e. a finder style menu
 */
 
 include_once("menuObject.php");
 
 class MenuButtonObject extends MenuObject
 {
 	var $m_imagePath;
 	
 	function setImagePath($imagePath)
 	{
 		$this->m_imagePath = $imagePath;
 	}
 	
 	function getImagePath()
 	{
 		return $this->m_imagePath;
 	}
 	function getHtmlSnippet()
 	{
 		$elementType = $this->m_elementType;
 		$class = $this->m_class;
 		$title = $this->m_title;
 		$id = $this->m_id;
 		$link = $this->m_link;
 		
 		# Convert from menu object to a html snippet
    	$menuSnippet = <<<MENU_SNIPPET
    	<$elementType 
MENU_SNIPPET;

		# Does this one have an id?
		if ($id != "none")
		{
			$menuSnippet .= <<<MENU_SNIPPET
			 id="$id"
MENU_SNIPPET;
		}
		
		# Does this one have class?
		if ($class != "none")
		{
	    	$menuSnippet .= <<<MENU_SNIPPET
    	class="$class"
MENU_SNIPPET;
		}

    	$menuSnippet .= <<<MENU_SNIPPET
    	><span><a onmouseover="ExpandButton('$id');" onmouseout="RetractButton('$id');"
MENU_SNIPPET;
		# Does this one have class?
		if ($class != "none")
		{
	    	$menuSnippet .= <<<MENU_SNIPPET
    	class="$class"
MENU_SNIPPET;
		}
    	$menuSnippet .= <<<MENU_SNIPPET
    	 href="$link">$title</a></span></$elementType>
MENU_SNIPPET;

		return $menuSnippet;
 	}
 }
 
 class ZoomMenu extends Menu
 {
 	function addButton($link, $title, $imagePath)
 	{
 		$menuItem = new MenuButtonObject();
 		$menuItem->setMenuValues("none", $link, $title, "none", "li");
 		$menuItem->setImagePath($imagePath);
 		
 		$this->addMenuObject($menuItem);
 	}
 	
	function createMenu()
	{
		$displayString = "<div><" . $this->m_elementType . " ";
		$id = $this->m_id;
		$class = $this->m_class;

		if ($id != "none")
		{		
			$displayString .= <<<MENU_SNIPPET
		 id="$id" 
MENU_SNIPPET;
		}
		
		if ($class != "none")
		{
			$displayString .= <<<MENU_SNIPPET
		  class="$class"
MENU_SNIPPET;
		}
		$displayString .= ">";
		
		$classCount = 1;
		foreach ($this->m_menuObjectArray as $menuObjectData)
		{
			$elementType = $menuObjectData->getElementType();
			$backgroundImage = $menuObjectData->getImagePath();
			# Create the class for this zoom menu
			$zoomClass = <<<CLASS_SNIPPET
			$elementType.zoommenu$classCount
			{
				background-image: url($backgroundImage);
			}

			a.zoommenu$classCount:link {color: #00ff00;}
			a.zoommenu$classCount:active {color: #0000ff;}
			a.zoommenu$classCount:visited {color: #ffffff;}
			a.zoommenu$classCount:hover {color: #000000; text-decoration: none; }
CLASS_SNIPPET;

			$styleData = new StyleSheetDataObject();
			
			$styleData->setData($zoomClass);
			$displayArray[] = $styleData;
			
			$menuObjectData->setClass("zoommenu" . $classCount);
			$menuObjectData->setId("zoommenuid" . $classCount);
			$classCount++;
			$displayString .= $menuObjectData->getHtmlSnippet();
		}
		$displayString .= "</";
		$displayString .= $this->m_elementType;
		$displayString .= "></div>";
		
		$bodyDisplay = new BodyObject();
		$bodyDisplay->setData($displayString);
		
		$displayArray[] = $bodyDisplay;
		
		# Add in java script for button expansion/retraction
		$javaObject = new JavaScriptLink();
		
		$javeFileName = "pageBuilder/zoomButton.js";
		$javaObject->setData($javeFileName);
		
		$displayArray[] = $javaObject;
		
		return $displayArray;
	}
 }
?>
