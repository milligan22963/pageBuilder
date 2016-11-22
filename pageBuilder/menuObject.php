<?php
/*
 * Created on Jun 5, 2007
 *
 * Author: 	daniel
 * File:   	menuObject.php
 * Project: pageBuilder
 * 
 * Purpose:
 * 		Used to create a menu object which can then be dumped to html/css objects
 * 		REQUIRES displayObject.php to be included prior.
 * 
 */
 $baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/displayObject.php';
 
 class MenuObject
 {
 	var $m_link;
 	var $m_id;
 	var $m_title;
 	var $m_class;
 	var $m_elementType;
 	var $m_additionalAttributes;
 	
 	function __construct()
 	{
 		$this->m_link = "none";
 		$this->m_id = "none";
 		$this->m_title = "none";
 		$this->m_class = "none";
 		$this->m_elementType = "li"; # default to a list item
 		$this->m_additionalAttributes = array();
 	}
 	
 	function setMenuValues($id, $link, $title, $class, $elementType)
 	{
 		$this->m_id = $id;
 		$this->m_link = $link;
 		$this->m_title = $title;
 		$this->m_class = $class;
 		$this->m_elementType = $elementType;
 	}
 
 	function addAttribute($name, $value)
 	{
 		$this->m_additionalAttributes[$name] = $value;
 	}
 	
 	function setLink($link)
 	{
 		$this->m_link = $link;
 	}
 	
 	function getLink()
 	{
 		return $this->m_link;
 	}
 	
 	function setId($id)
 	{
 		$this->m_id = $id;
 	}
 	
 	function getId()
 	{
 		return $this->m_id;
 	}
 	
 	function setTitle($title)
 	{
 		$this->m_title = $title;
 	}
 	
 	function getTitle()
 	{
 		return $this->m_title;
 	}
 	
 	function setClass($class)
 	{
 		$this->m_class = $class;
 	}
 	
 	function getClass()
 	{
 		return $this->m_class;
 	}
 	
 	function setElementType($elementType)
 	{
 		$this->m_elementType = $elementType;
 	}
 	
 	function getElementType()
 	{
 		return $this->m_elementType;
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
		
		# any additional attribtues?
		foreach ($this->m_additionalAttributes as $name=>$value)
		{
			$menuSnippet .=<<<MENU_SNIPPET
			 $name="$value" 
MENU_SNIPPET;
		}

		# Does this one have class?
		if ($class != "none")
		{
	    	$menuSnippet .= <<<MENU_SNIPPET
    	class="$class"><span><a class="$class"
MENU_SNIPPET;
		}
		else
		{
	    	$menuSnippet .= <<<MENU_SNIPPET
    	><span><a 
MENU_SNIPPET;
		}

		$menuSnippet .= <<<MENU_SNIPPET
    	href="$link">$title</a></span></$elementType>
MENU_SNIPPET;
		return $menuSnippet;
 	}
 }
 
 
 class Menu
 {
 	var $m_menuObjectArray;
 	var $m_numberMenuObjects;
 	var	$m_elementClass;
 	var $m_divClass;
 	var $m_id;
 	var $m_elementType;
 	private $m_menuHeader;
 	private $m_menuHeaderClass;

 	function __construct()
 	{
 		$this->Menu();
 	}
 	
 	function Menu()
 	{
 		$this->m_menuObjectArray = array();
 		$this->m_numberMenuObjects = 0;
 		$this->m_elementClass = "none";
 		$this->m_divClass = "none";
 		$this->m_id = "none";
 		$this->m_elementType = "ul";
 		$this->m_menuHeader = null;
 		$this->m_menuHeaderClass = null;
 	}
 	
	function setMenuValues($id, $divClass, $elementClass, $elementType)
	{
		$this->m_elementClass = $elementClass;
		$this->m_divClass = $divClass;
		$this->m_id = $id;
		$this->m_elementType = $elementType;
	}
	
	function addMenuObject(& $menuObject)
	{
		$this->m_menuObjectArray[$this->m_numberMenuObjects] = $menuObject;
		$this->m_numberMenuObjects++;
	}
	
	function setMenuHeader($menuHeader, $menuHeaderClass)
	{
 		$this->m_menuHeader = $menuHeader;
 		$this->m_menuHeaderClass = $menuHeaderClass;
	}
	
	function renderMenu()
	{
		$displayString = '<div';
		if ($this->m_menuHeader != null)
		{
			$displayString .= ' class="' . $this->m_menuHeaderClass . '">' . $this->m_menuHeader . '</div>';
			$displayString .= '<hr class="extensionline"/><nav ';
		}
		else
		{
			$displayString = '<nav';
		}
		if ($this->m_divClass != "none")
		{
			$displayString .= " class=\"" . $this->m_divClass . "\"";
		}
		$displayString .= "><" . $this->m_elementType . " ";
		$id = $this->m_id;
		$class = $this->m_elementClass;

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
		
		foreach ($this->m_menuObjectArray as $menuObjectData)
		{
			$displayString .= $menuObjectData->getHtmlSnippet();
		}
		$displayString .= "</";
		$displayString .= $this->m_elementType;
		$displayString .= "></nav>";
		
		$bodyDisplay = new BodyObject();
		$bodyDisplay->setData($displayString);
		
		$displayArray[] = $bodyDisplay;
		return $displayArray;
	}
 }
?>
