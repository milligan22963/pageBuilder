<?php

/*
 * Theme.php
 * 
 * To manage theme related information assumes that there will be a description.xml file or the like
 * to describe this theme including some of the following info:
 * 
 * <p:name>AFM</p:name>
 * <p:description>The default theme for this package</p:description>
 * <p:stylesheet>afm.css</p:stylesheet>  - can be one or more
 * <p:images>images</p:images>
 * <p:javascript>js</p:javascript>
 * <p:header>header.php</p:header>
 * <p:headerwidget display="pre">header_wdg.php</p:headerwidget>
 * 
 * display can be either pre (before the header) or post (after the header)
 */
$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/xmlPage.php';

define('THEME_FILE_NAME', "description.xml");
define ('STYLE_SHEET_ACTIVE', 1);
define ('STYLE_SHEET_INACTIVE', 0);

function loadThemeScript(& $page, $scriptName)
{
	$systemObject = System::getInstance();

	$jsl = $page->addJavaScriptLink($scriptName);
	$siteFlatten = $systemObject->getConfigurationData(SITE_FLATTEN_JS);
	if ($siteFlatten == "true")
	{
		$jsl->setCanFlatten(true); // default to flatten
	}
	else
	{
		$jsl->setCanFlatten(false); // default to flatten
	}	
}

class Theme
{
	private $m_themeFile;
	private $m_name;
	private $m_description;
	private $m_styleSheet; /* array of local file names */
	private $m_imageDirectory; /* relative to the theme path */
	private $m_jsDirectory; /* relative to the theme path */
	private $m_functionFile; /* file of functions specific to this theme */
	
	private $m_headerFile;
	private $m_headerWidgetFile;
	private $m_displayHeaderWidget;
	private $m_bodyFile;
	private $m_bodyWidgetFile;
	private $m_displayBodyWidget;
	private $m_footerFile;
	private $m_footerWidgetFile;
	private $m_displayFooterWidget;
	
	/*
	 * PHP 5 constructor
	 */
	function __construct()
	{
		$this->Theme();
	}
	
	/*
	 * PHP 4 constructor
	 */
	function Theme()
	{
		$this->m_themeFile = null;
		$this->m_name = "default";
		$this->m_description = "Default Data";
		$this->m_styleSheet = array();
		$this->m_imageDirectory = null;
		$this->m_jsDirectory = null;
		$this->m_functionFile = null;
		$this->m_headerFile = null;
		$this->m_headerWidgetFile = null;
		$this->m_displayHeaderWidget = DISPLAY_POST;
		$this->m_bodyFile = null;
		$this->m_bodyWidgetFile = null;
		$this->m_displayBodyWidget = DISPLAY_POST;
		$this->m_footerFile = null;
		$this->m_footerWidgetFile = null;
		$this->m_displayFooterWidget = DISPLAY_POST;
	}
		
	function loadThemeFile($fileName)
	{
		$xmlPage = new XmlPageData();
		
		$xmlPage->loadXmlFile($fileName);
		
		/*
		 * Need to pull out the theme file data including
		 *   Name
		 *   Description
		 *   StyleSheet(s)
		 *   ImageDirectory
		 *   JavaScript Directory
		 */
		$childObject = $xmlPage->findChild("name");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setName($valueTextObject->getValue());
//				print "Name: " . $valueTextObject->getValue() . "<br />";
			}
		}
		
		$childObject = $xmlPage->findChild("description");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setDescription($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("stylesheet");
		if ($childObject != null)
		{
			foreach ($childObject as $child)
			{
				$valueTextObject = $child->getChildObject(0);
				if ($valueTextObject != null)
				{
					$this->addStyleSheet($valueTextObject->getValue());
				}
			}
		}
		
		$childObject = $xmlPage->findChild("images");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setImageDirectory($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("javascript");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setJavaScriptDirectory($valueTextObject->getValue());
			}
		}

		$childObject = $xmlPage->findChild("functions");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setFunctionFile($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("headerfile");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setHeaderFile($valueTextObject->getValue());
			}
		}

		$childObject = $xmlPage->findChild("headerwidgetfile");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			
			if ($valueTextObject != null)
			{
				$this->setHeaderWidgetFile($valueTextObject->getValue());
			}
			
			if ($childObject[0]->getChildCount() > 1)
			{
				$valueTextObject = $childObject[0]->getChildObject(1);
				$this->setHeaderWidgetDisplay($valueTextObject->getChildObject(0)->getValue());
			}
		}

		$childObject = $xmlPage->findChild("bodyfile");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setBodyFile($valueTextObject->getValue());
			}
		}

		$childObject = $xmlPage->findChild("bodywidgetfile");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setBodyWidgetFile($valueTextObject->getValue());
			}
			
			if ($childObject[0]->getChildCount() > 1)
			{
				$valueTextObject = $childObject[0]->getChildObject(1);
				$this->setBodyWidgetDisplay($valueTextObject->getChildObject(0)->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("footerfile");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setFooterFile($valueTextObject->getValue());
			}
		}

		$childObject = $xmlPage->findChild("footerwidgetfile");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setFooterWidgetFile($valueTextObject->getValue());
			}
			
			if ($childObject[0]->getChildCount() > 1)
			{
				$valueTextObject = $childObject[0]->getChildObject(1);
				$this->setFooterWidgetDisplay($valueTextObject->getChildObject(0)->getValue());
			}
		}
		
		$this->setThemeFile($fileName);
	}
	
	function saveThemeFile($fileName)
	{
		$targetFileName = $this->getThemeFile();
		if ($fileName != null)
		{
			$targetFileName = $fileName;
		}
		
		$xmlPage = new XmlPageData();
		$xmlPage->setName("theme");

		$childDataObject = new XmlDataObject();
		$childDataObject->setName("name");
		$childDataObject->setValue($this->getName());
		$xmlPage->addChildObject($childDataObject);

		$childDataObject = new XmlDataObject();
		$childDataObject->setName("description");
		$childDataObject->setValue($this->getDescription());
		$xmlPage->addChildObject($childDataObject);
		
		foreach ($this->m_styleSheet as $styleSheet => $active)
		{
			if ($active == STYLE_SHEET_ACTIVE)
			{
				$childDataObject = new XmlDataObject();
				$childDataObject->setName("stylesheet");
				$childDataObject->setValue($styleSheet);
			}
		}

		if ($this->getImageDirectory() != null)
		{
			$childDataObject = new XmlDataObject();
			$childDataObject->setName("images");
			$childDataObject->setValue($this->getImageDirectory());
			$xmlPage->addChildObject($childDataObject);
		}

		if ($this->getJavaScriptDirectory() != null)
		{
			$childDataObject = new XmlDataObject();
			$childDataObject->setName("javascript");
			$childDataObject->setValue($this->getJavaScriptDirectory());
			$xmlPage->addChildObject($childDataObject);
		}
		
		if ($this->getFunctionFile() != null)
		{
		    $childDataObject = new XmlDataObject();
			$childDataObject->setName("functions");
			$childDataObject->setValue($this->getFunctionFile());
			$xmlPage->addChildObject($childDataObject);
		}
		
		if ($this->getHeaderFile() != null)
		{
		    $childDataObject = new XmlDataObject();
			$childDataObject->setName("headerfile");
			$childDataObject->setValue($this->getHeaderFile());
			$xmlPage->addChildObject($childDataObject);
		}
		
		if ($this->getHeaderWidgetFile() != null)
		{
			$childDataObject = new XmlDataObject();
			$childDataObject->setName("headerwidgetfile");
			$childDataObject->setValue($this->getHeaderWidgetFile());
			$xmlPage->addChildObject($childDataObject);
		}
		
		if ($this->getBodyFile() != null)
		{
		    $childDataObject = new XmlDataObject();
			$childDataObject->setName("bodyfile");
			$childDataObject->setValue($this->getBodyFile());
			$xmlPage->addChildObject($childDataObject);
		}
		
		if ($this->getBodyWidgetFile() != null)
		{
			$childDataObject = new XmlDataObject();
			$childDataObject->setName("bodywidgetfile");
			$childDataObject->setValue($this->getBodyWidgetFile());
			$xmlPage->addChildObject($childDataObject);
		}

		if ($this->getFooterFile() != null)
		{
		    $childDataObject = new XmlDataObject();
			$childDataObject->setName("footerfile");
			$childDataObject->setValue($this->getFooterFile());
			$xmlPage->addChildObject($childDataObject);
		}
		
		if ($this->getFooterWidgetFile() != null)
		{
			$childDataObject = new XmlDataObject();
			$childDataObject->setName("footerwidgetfile");
			$childDataObject->setValue($this->getFooterWidgetFile());
			$xmlPage->addChildObject($childDataObject);
		}
				
		$xmlPage->setDirectDisplay(false);
		
		$xmlPageData = $xmlPage->renderPage();
		$fileHandle = fopen($targetFileName, "w+");
		if ($fileHandle != false)
		{
			fwrite($fileHandle, $xmlPageData);
		}
		else
		{
			print "Write failed to: " . $targetFileName;
		}
	}
	
	function setThemeFile($fileName)
	{
		$this->m_themeFile = $fileName;
	}
	
	function getThemeFile()
	{
		return $this->m_themeFile;
	}
	
	function setName($themeName)
	{
		$this->m_name = $themeName;
	}
	
	function getName()
	{
		return $this->m_name;
	}
	
	function setDescription($themeDescription)
	{
		$this->m_description = $themeDescription;
	}
	
	function getDescription()
	{
		return $this->m_description;
	}
	
	function addStyleSheet($styleSheet)
	{
		$this->m_styleSheet[$styleSheet] = STYLE_SHEET_ACTIVE;
	}
	
	function removeStyleSheet($styleSheet)
	{
		if (array_key_exists($styleSheet, $this->m_styleSheet))
		{
			$this->m_styleSheet[$styleSheet] = STYLE_SHEET_INACTIVE;
		}
	}
	
	function getStyleSheets()
	{
		
		$retArray = array();
		
		foreach ($this->m_styleSheet as $name=>$active)
		{
			if ($active == STYLE_SHEET_ACTIVE)
			{
				$retArray[$name] = STYLE_SHEET_ACTIVE;
			}
		}
		return $retArray;
	}
	
	function setImageDirectory($directory)
	{
		$this->m_imageDirectory = $directory;
	}
	
	function getImageDirectory()
	{
		return $this->m_imageDirectory;
	}
	
	function setJavaScriptDirectory($directory)
	{
		$this->m_jsDirectory = $directory;
	}
	
	function getJavaScriptDirectory()
	{
		return $this->m_jsDirectory;
	}
	
	function setHeaderFile($headerFile)
	{
		$this->m_headerFile = $headerFile;
	}
	
	function getHeaderFile()
	{
		return $this->m_headerFile;
	}
	
	function setHeaderWidgetFile($headerWidgetFile)
	{
		$this->m_headerWidgetFile = $headerWidgetFile;
	}
	
	function getHeaderWidgetFile()
	{
		return $this->m_headerWidgetFile;
	}

	function setHeaderWidgetDisplay($headerWidgetDisplay)
	{
		$this->m_displayHeaderWidget = $headerWidgetDisplay;
	}
	
	function getHeaderWidgetDisplay()
	{
		return $this->m_displayHeaderWidget;
	}
	
	function setBodyFile($bodyFile)
	{
		$this->m_bodyFile = $bodyFile;
	}
	
	function getBodyFile()
	{
		return $this->m_bodyFile;
	}
	
	function setBodyWidgetFile($bodyWidgetFile)
	{
		$this->m_bodyWidgetFile = $bodyWidgetFile;
	}

	function getBodyWidgetFile()
	{
		return $this->m_bodyWidgetFile;
	}

	function setBodyWidgetDisplay($bodyWidgetDisplay)
	{
		$this->m_displayBodyWidget = $bodyWidgetDisplay;
	}
	
	function getBodyWidgetDisplay()
	{
		return $this->m_displayBodyWidget;
	}
		
	function setFooterFile($footerFile)
	{
		$this->m_footerFile = $footerFile;
	}
	
	function getFooterFile()
	{
		return $this->m_footerFile;
	}
	
	function setFooterWidgetFile($footerWidgetFile)
	{
		$this->m_footerWidgetFile = $footerWidgetFile;
	}
	
	function getFooterWidgetFile()
	{
		return $this->m_footerWidgetFile;
	}
	
	function setFooterWidgetDisplay($footerWidgetDisplay)
	{
		$this->m_displayFooterWidget = $footerWidgetDisplay;
	}
	
	function getFooterWidgetDisplay()
	{
		return $this->m_displayFooterWidget;
	}
	
	function setFunctionFile($functionFile)
	{
		$this->m_functionFile = $functionFile;
	}
	
	function getFunctionFile()
	{
		return $this->m_functionFile;
	}
};
?>