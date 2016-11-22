<?php
/*
 * Extension base class
 */
$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/xmlPage.php';

define('EXTENSION_FILE_NAME', "description.xml");
define ('EXT_STYLE_SHEET_ACTIVE', 1);
define ('EXT_STYLE_SHEET_INACTIVE', 0);
define('EXTENSION_ACTIVE', 1);
define('EXTENSION_INACTIVE', 0);

/* 
 * currently known types
 */
define('EXTENSION_UNKNOWN', 'UNKNOWN');
define('EXTENSION_MENU', 'MENU');
define('EXTENSION_LOGIN', 'LOGIN');
define('EXTENSION_GALLERY', 'GALLERY');
define('EXTENSION_GADGET', 'GADGET');
define('EXTENSION_WIDGET', 'WIDGET');
define('EXTENSION_ANIMATION', 'ANIMATION');

class Extension
{
	private $m_extensionFile; /* the actual extension file (description) that was loaded */
	private $m_name;
	private $m_displayName; /* the actual name to display */
	private $m_description;
	private $m_styleSheet; /* array of local file names */
	private $m_imageDirectory; /* relative to the theme path */
	private $m_jsDirectory; /* relative to the theme path */
	private $m_codeFile; /* the code that is the extension */
	private $m_active; /* Boolean indicating if active or not */
	private $m_displayPosition; /* display position i.e. PRE or POST */
	private $m_displayLocation; /* display location i.e. HEADER, BODY, FOOTER */
	private $m_installDirectory; /* where the files for installing this extension are found */
	private $m_installCode; /* The file to load to install the extension */
	private $m_website;
	private $m_updateLink;
	private $m_extensionPath; /* THe path to the extension */
	private $m_extensionUrl; /* the url to the extensions */
	private $m_extensionCallbacks; /* array of callbacks for the extension */
	private $m_extensionInstance; /* The instance id of this extension */
	private $m_allowMultipleInstances; /* true if multiple intstances are allowed, false otherwise */
	private $m_extensionType; /* enumerated list of extension types */
	private $m_showTitle;
	private $m_paramArray;
	
	function __construct()
	{
		$this->m_paramArray = null;
		$this->m_extensionFile = null;
		$this->m_name = "default";
		$this->m_description = "default extension description";
		$this->m_styleSheet = array();
		$this->m_imageDirectory = null;
		$this->m_jsDirectory = null;
		$this->m_codeFile = null;
		$this->m_active = false;
		$this->m_displayPosition = DISPLAY_POST;
		$this->m_displayLocation = NO_WIDGET_AREA;
		$this->m_installDirectory = "install";
		$this->m_installCode = "install.php";
		$this->m_website = null;
		$this->m_updateLink = null;
		$this->m_displayName = "default";
		$this->m_extensionPath = null;
		$this->m_extensionUrl = null;
		$this->m_extensionCallbacks = array();
		$this->m_extensionInstance = 1;
		$this->m_allowMultipleInstances = false;
		$this->m_extensionType = EXTENSION_UNKNOWN; // default
		$this->m_showTitle = true;
	}
	
/* failed due to non-object
	function __clone()
	{
		$this->m_extensionCallbacks = clone $this->m_extensionCallbacks;
		$this->m_styleSheet = clone $this->m_styleSheet;
	}
	*/
	function setAllowMultipleInstances($allow)
	{
		if (strncasecmp($allow, "true", 4) == 0)
		{
			$this->m_allowMultipleInstances = true;
		}
		else
		{
			$this->m_allowMultipleInstances = false;
		}
	}
	
	function getAllowMultipleInstances()
	{
		return $this->m_allowMultipleInstances;
	}
	
	function setInstance($instance)
	{
		$this->m_extensionInstance = $instance;
	}
	
	function getInstance()
	{
		return $this->m_extensionInstance;
	}
	
	function setInstallCode($fileName)
	{
		$this->m_installCode = $fileName;
	}
	
	function getInstallCode()
	{
		return $this->m_installCode;
	}
	
	function setInstallDirectory($directory)
	{
		$this->m_installDirectory = $directory;
	}
	
	function getInstallDirectory()
	{
		return $this->m_installDirectory;
	}
	
	function setWebsite($website)
	{
		$this->m_website = $website;
	}
	
	function getWebsite()
	{
		return $this->m_website;
	}
	
	function setExtensionType($extensionType)
	{
		$this->m_extensionType = $extensionType;
	}
	
	function getExtensionType()
	{
		return $this->m_extensionType;
	}
	
	function setExtensionUrl($extensionUrl)
	{
		$this->m_extensionUrl = $extensionUrl;
	}
	
	function getExtensionUrl()
	{
		return $this->m_extensionUrl;
	}

	function setExtensionPath($extensionPath)
	{
		$this->m_extensionPath = $extensionPath;
	}
	
	function getExtensionPath()
	{
		return $this->m_extensionPath;
	}
	
	function setUpdateLink($updateLink)
	{
		$this->m_updateLink = $updateLink;
	}
	
	function getUpdateLink()
	{
		return $this->m_updateLink;
	}
	
	function setDisplayPosition($displayPosition)
	{
		if (($displayPosition == DISPLAY_PRE) || ($displayPosition == DISPLAY_INSIDE) || ($displayPosition == DISPLAY_POST))
		{
			$this->m_displayPosition = $displayPosition;
		}
	}
	
	function getParamArray()
	{
		return $this->m_paramArray;
	}

	function setParamArray($paramArray)
	{
		$this->m_paramArray = $paramArray;
	}
	
	function getDisplayPosition()
	{
		return $this->m_displayPosition;
	}
	
	function setDisplayLocation($displayLocation)
	{
		switch ($displayLocation)
		{
			case HEADER_WIDGET_AREA:
			case HEADER_CONTENT_AREA:
			case BODY_WIDGET_AREA:
			case BODY_CONTENT_AREA:
			case FOOTER_WIDGET_AREA:
			case FOOTER_CONTENT_AREA:
				$this->m_displayLocation = $displayLocation;
				break;
		}
	}
	
	function getDisplayLocation()
	{
		return $this->m_displayLocation;
	}
	
	function setExtensionFile($fileName)
	{
		$this->m_extensionFile = $fileName;
	}
	
	function getExtensionFile()
	{
		return $this->m_extensionFile;
	}
	
	function setName($extensionName)
	{
		$this->m_name = $extensionName;
	}
	
	function getName()
	{
		return $this->m_name;
	}
	
	function setDisplayName($displayName)
	{
		$this->m_displayName = $displayName;
	}
	
	function getDisplayName()
	{
		return $this->m_displayName;
	}
	
	function setDescription($extensionDescription)
	{
		$this->m_description = $extensionDescription;
	}
	
	function getDescription()
	{
		return $this->m_description;
	}
	
	function addStyleSheet($styleSheet)
	{
		$this->m_styleSheet[$styleSheet] = EXT_STYLE_SHEET_ACTIVE;
	}
	
	function removeStyleSheet($styleSheet)
	{
		if (array_key_exists($styleSheet, $this->m_styleSheet))
		{
			$this->m_styleSheet[$styleSheet] = EXT_STYLE_SHEET_INACTIVE;
		}
	}
	
	function getStyleSheets()
	{
		
		$retArray = array();
		
		foreach ($this->m_styleSheet as $name=>$active)
		{
			if ($active == EXT_STYLE_SHEET_ACTIVE)
			{
				$retArray[$name] = STYLE_SHEET_ACTIVE;
			}
		}
		return $retArray;
	}
	
	function includeStyleSheets(& $page)
	{
		$extensionPath = $this->getExtensionUrl() . "/";
		
		/* 
		 * Does this extension rely on any style sheets?  If so then add them in
		 */
		foreach ($this->m_styleSheet as $name=>$active)
		{
			if ($active == EXT_STYLE_SHEET_ACTIVE)
			{
				$styleSheetName = $extensionPath . $name;
				$page->addStyleSheet($styleSheetName);
			}
		}
	}
	
	function requireScript(& $page, $scriptName)
	{
		if (loadScript($scriptName, null, $page) == false)
		{
			$systemObject = System::getInstance();
			// not a shortname so it is relative to this extension
			$javaScriptFile = $this->getExtensionUrl() . "/" . $this->getJavaScriptDirectory() . "/" . $scriptName;
			$jsl = $page->addJavaScriptLink($javaScriptFile);
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
	
	function setCodeFile($codeFile)
	{
		$this->m_codeFile = $codeFile;
	}
	
	function getCodeFile()
	{
		return $this->m_codeFile;
	}
	
	function setShowTitle($showTitle)
	{
		$this->m_showTitle = $showTitle;
	}
	
	function getShowTitle()
	{
		return $this->m_showTitle;
	}
	
	function registerCallback($index, $method)
	{
		$this->m_extensionCallbacks[$index] = $method;
	}

	function loadExtensionFile($fileName)
	{
		$xmlPage = new XmlPageData();
		
		$xmlPage->loadXmlFile($fileName);
		
		/*
		 * Need to pull out the extension file data including
		 *   Name
		 *   Description
		 *   StyleSheet(s)
		 *   ImageDirectory
		 *   JavaScript Directory
		 *   code file
		 */
		$childObject = $xmlPage->findChild("name");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setName($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("displayname");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setDisplayName($valueTextObject->getValue());
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
		
		$childObject = $xmlPage->findChild("multiinstance");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setAllowMultipleInstances($valueTextObject->getValue());
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

		$childObject = $xmlPage->findChild("codefile");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setCodeFile($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("installarea");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setInstallDirectory($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("installfile");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setInstallCode($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("website");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setWebsite($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("updateLink");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setUpdateLink($valueTextObject->getValue());
			}
		}
		
		$childObject = $xmlPage->findChild("extensionType");
		if ($childObject != null)
		{
			$valueTextObject = $childObject[0]->getChildObject(0);
			if ($valueTextObject != null)
			{
				$this->setExtensionType($valueTextObject->getValue());
			}
		}
		
		$this->setExtensionFile($fileName);
	}
	
	function saveExtensionFile($fileName)
	{
		$targetFileName = $this->getExtensionFile();
		if ($fileName != null)
		{
			$targetFileName = $fileName;
		}
		
		$xmlPage = new XmlPageData();
		$xmlPage->setName("extension");

		$childDataObject = new XmlDataObject();
		$childDataObject->setName("name");
		$childDataObject->setValue($this->getName());
		$xmlPage->addChildObject($childDataObject);

		$childDataObject = new XmlDataObject();
		$childDataObject->setName("description");
		$childDataObject->setValue($this->getDescription());
		$xmlPage->addChildObject($childDataObject);
		
		$childDataObject = new XmlDataObject();
		$childDataObject->setName("multiinstance");
		if ($this->getAllowMultipleInstances() == true)
		{
			$childDataObject->setValue("true");
		}
		else
		{
			$childDataObject->setValue("false");
		}
		$xmlPage->addChildObject($childObject);
		
		foreach ($this->m_styleSheet as $styleSheet => $active)
		{
			if ($active == EXT_STYLE_SHEET_ACTIVE)
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
		
		if ($this->getCodeFile() != null)
		{
		    $childDataObject = new XmlDataObject();
			$childDataObject->setName("codefile");
			$childDataObject->setValue($this->getCodeFile());
			$xmlPage->addChildObject($childDataObject);
		}
		
		if ($this->getDisplayName() != "default")
		{
			$childDataObject = new XmlDataObject();
			$childDataObject->setName("displayname");
			$childDataObject->setValue($this->getDisplayName());
			$xmlPage->addChildObject($childDataObject);
		}
		
		$childDataObject = new XmlDataObject();
		$childDataObject->setName("installarea");
		$childDataObject->setValue($this->getInstallDirectory());
		$xmlPage->addChildObject($childDataObject);
		
		$childDataObject = new XmlDataObject();
		$childDataObject->setName("installfile");
		$childDataObject->setValue($this->getInstallCode());
		$xmlPage->addChildObject($childDataObject);
		
		$childDataObject = new XmlDataObject();
		$childDataObject->setName("extensionType");
		$childDataObject->setValue($this->getExtensionType());
		$xmlPage->addChildObject($childDataObject);
		
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
	
	function isActive()
	{
		return $this->m_active;
	}
	
	function setActive()
	{
		$this->m_active = true;
	}
	
	function setInActive()
	{
		$this->m_active = false;
	}
	
	/*
	 * load
	 * 
	 * this is used to load this extension prior to it being displayed.  This would only be called if this
	 * is active in the system
	 */
	function load()
	{
		$this->m_active = true; /* If we are loading it then it is active */
		$this->registerCallback("SetDisplayOptions", "setDisplayOptions");
	}
	
	/*
	 * activate
	 * 
	 * this is used to activate the extension in the system. it is expected to be done when this
	 * extension is activated
	 */
	function activate()
	{
		$systemObject = System::getInstance();
		
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$dbInstance = $systemObject->getDbInstance();
		
		$this->m_active = true;
		$extResourceId = 0;
		
		$queryString = "select *, cast(`active` as unsigned integer) as `activeFlag` from " . $tablePrefix . "extension where `class`='" . $this->getName() . "' order by `instance` desc;";
		if ($dbInstance->issueCommand($queryString, $extResourceId) == true)
		{
			$resultSet = $dbInstance->getResult($extResourceId);
			// if we don't have any then we are good to insert it
			if ($resultSet->rowCount() == 0)
			{
				$queryString = "insert into " . $tablePrefix . "extension (`class`, `instance`, `active`, `extensionTimeStamp`) values ";
				$queryString .= " ('" . $this->getName() . "', 1, b'1', CURRENT_TIMESTAMP);";
				$dbInstance->issueCommand($queryString);
			}
			else
			{
				$activeCount = 0;
				$instanceValue = 0;
				$reuseInstance = 0;

				// we already have at least one, is it active?
				$found = false;
				while ($row = $resultSet->fetch(PDO::FETCH_LAZY))
				{
					$instanceValue = (int)$row->instance;
					if ($row->activeFlag == 0)
					{
						$reuseInstance = $instanceValue;
					}
					else
					{
						$activeCount++; // keep track of how many are active 
					}
				}

				// if we allow multiples or there are not any active ones
				if (($this->getAllowMultipleInstances() == true) || ($activeCount == 0))
				{
					// If we didnt find any then add a new one otherwise reuse an existing one that is inactive
					if ($reuseInstance == 0)
					{
						$instanceValue++;
						$queryString = "insert into " . $tablePrefix . "extension (`class`, `instance`, `active`, `extensionTimeStamp`) values ";
						$queryString .= " ('" . $this->getName() . "'," . $instanceValue . ", b'1', CURRENT_TIMESTAMP);";
						$dbInstance->issueCommand($queryString);
					}
				}
				$this->setInstance($instanceValue);
				$dbInstance->releaseResults($extResourceId);
			}
			
			$queryString = "update " . $tablePrefix . "extension set `active`=b'1', ";
			$queryString .= " `position`='" . $this->getDisplayPosition() . "', ";
			$queryString .= " `location`='" . $this->getDisplayLocation() . "'";
			$queryString .= " where `class`='" . $this->getName() . "' and `instance`=" . $this->getInstance() . ";";
//			error_log($queryString);
			$dbInstance->issueCommand($queryString);
		}
	}
	
	/*
	 * deactivate
	 * 
	 * this is used to deactivate an extension in the system.  it is expected to be done when this
	 * extension is de-activated
	 */
	function deactivate()
	{
		$systemObject = System::getInstance();
		
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$dbInstance = $systemObject->getDbInstance();
		
		$this->m_active = false;
		
//		error_log("Deactivating: " . $this->getName() . " Instance: " . $this->getInstance());
		$queryString = "select * from " . $tablePrefix . "extension where `class`='" . $this->getName() . "' and `instance`=" . $this->getInstance() . ";";
		if ($dbInstance->issueCommand($queryString) == true)
		{
			$resultSet = $dbInstance->getResult();
			if ($resultSet->rowCount() > 0)
			{
				$queryString = "update " . $tablePrefix . "extension set `active`=b'0' ";
				$queryString .= " where `class`='" . $this->getName() . "' and `instance`=" . $this->getInstance() . ";";
				$dbInstance->issueCommand($queryString);
			}
			$dbInstance->releaseResults();
		}
	}
	
	/*
	 * getCommandResults
	 */
	function getCommandResults($success)
	{
		$xmlPage = new XmlPageData();
		$xmlPage->setName($this->getName());
		if ($success == true)
		{
			$xmlPage->addChild("results", "1");
		}
		else
		{
			$xmlPage->addChild("results", "0");
		}
				
		return $xmlPage;
	}
	
	/*
	 * processCommand
	 * 
	 * @param $command - the command to process assocaited with this extension
	 * @param $paramArray - the array of parameters passed by the caller
	 * 
	 * @return data to send back to the caller
	 */
	function processCommand($command, $paramArray)
	{
		$returnData = null;
		
		if (array_key_exists($command, $this->m_extensionCallbacks))
		{
			/*
			 * $userArray = array($this=>$this->m_extensionCallbacks[$command]);
			 * call_user_func($userArray, $paramString);
			 */
			 $userArray = array($this, $this->m_extensionCallbacks[$command]);
			 $returnData = call_user_func($userArray, $paramArray);
			//			$this->m_extensionCallbacks[$command]($paramArray);
//			$this->${$this->m_extensionCallbacks[$command]}($paramArray);
		}
		
		return $returnData;
	}
	
	function setDisplayOptions($paramArray)
	{
		// another extension wants to change my display
		if (array_key_exists('location', $paramArray) == true)
		{
			$this->setDisplayLocation($paramArray['location']);
		}
		if (array_key_exists('position', $paramArray) == true)
		{
			$this->setDisplayPosition($paramArray['position']);
		}		
	}
	
	/*
	 * preDisplay - called to prepare anything required prior to display being called such as adding menu options dynamically etc
	 * 
	 * @param any parameters being passed into the extension(s)
	 */
	function preDisplay()
	{
	}
	
	/*
	 * postDisplay - called to clean up anything that might have occurred like saving settings and the like
	 * 
	 */
	function postDisplay()
	{
		//
	}
	
	/*
	 * display
	 * 
	 * this is used to display the extension and its data i.e. forms etc.  This will only be called if this item
	 * is active in the system
	 * 
	 * @param page which is the page to display data to
	 * 
	 * @return nothing
	 */
	function display(& $page)
	{
		$this->includeStyleSheets($page);
		
		if ((function_exists("getWidgetHeader") && $this->getShowTitle() == true))
		{
			$widgetHeaderText = getWidgetHeader($this->getDisplayName());
			
			$page->addBodyData($widgetHeaderText);
		}
	}
}

class ExtensionManager
{
	static private $m_instance = null;
	private $m_extensionArray = null; /* the array of known extensions */
	private $m_instantiatedExtensionArray = null; /* the array of known extensions that have been instantiated */
	
	private function ExtensionManager()
	{
		$this->m_extensionArray = array();
		$this->m_instantiatedExtensionArray = array();
	}
	
	private function __construct()
	{
		$this->ExtensionManager();
	}
	
	static public function getInstance()
	{
		if (self::$m_instance == null)
		{
			self::$m_instance = new ExtensionManager();
		}
		
		return self::$m_instance;
	}
	
	function dumpExtensions(& $page)
	{
		// For debugging/testing - dump out all loaded extensions
		foreach ($this->m_extensionArray as $index=>$object)
		{
			$bodyData = "<div>" . $object->getName() . "</div>";
			$page->addBodyData($bodyData);
		}
	}
	
	function getExtensions($state)
	{
		$returnArray = array();
		
		if ($state == EXTENSION_INACTIVE)
		{
			foreach ($this->m_extensionArray as $name=>$object)
			{
				if (($object->getAllowMultipleInstances() == true) || ($object->isActive() == false))
				{
					$returnArray[$name] = $object;
				}	
			}
		}
		if ($state == EXTENSION_ACTIVE)
		{
			foreach ($this->m_instantiatedExtensionArray as $name=>$object)
			{
				if ($object->isActive() == true)
				{
					$returnArray[$name] = $object;
				}
			}
		}
		return $returnArray;
	}
	
	/*
	 * loadExtension
	 * 
	 * This will load all of the extensions in the extension path
	 * but will not instantiate them.  They will be instaniated when registered
	 * and if activated as defined in the db.
	 */
	function loadExtensions($extensionPath)
	{
		// Load all of the extensions in the specified path
		$extensions = scandir($extensionPath);
		foreach ($extensions as $index=>$name)
		{
			$path = $extensionPath . $name;		
			if (is_dir($path))
			{
				if (($name != ".") && ($name != ".."))
				{
//					error_log("Registering: " . $path);
					$this->registerExtension($path);
				}
			}
		}
	}
	
	function registerExtension($extPath, $fileName = EXTENSION_FILE_NAME)
	{
		$systemObject = System::getInstance();
		
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		
		$extensionFile = $extPath . "/" . $fileName;
		
		// Place this extension in the database if it doesn't yet exist
		$extension = new Extension();
		$extension->loadExtensionFile($extensionFile);
		
		$extensionName = $extension->getName();
		$codeFile = $extension->getCodeFile();
		
		/* Include the extension code file then instantiate it */
		//error_log("Including: " . $extPath . '/' . $codeFile);
		
		include_once $extPath . "/" . $codeFile;

		/*
		 * Instantiate this object which then requires the new object to be loaded in affect causing
		 * the extension file.xml to be loaded twice per page load.
		 * this will also happen during ajax requests.  Need to be able to clone the object
		 * however if the end user overrides the loadExtensionFile we then will have issues
		 * perhaps a way to only load what we need in the first call...
		 * 
		 */
		$derivedExtension = new $extensionName;
		$derivedExtension->loadExtensionFile($extensionFile);
		$derivedExtension->setExtensionUrl($systemObject->getExtensionPath(false) . basename($extPath));
		$derivedExtension->setExtensionPath($systemObject->getBaseSystemDir() . "/" . $systemObject->getExtensionPath(true) . basename($extPath));
		
		/* This is the main array of extensions available */
		$this->m_extensionArray[$extensionName] = $derivedExtension;
		
		// See if it has been activated yet
		$queryString = "select *, cast(`active` as unsigned integer) as `activeFlag` from " . $tablePrefix . "extension where `class`='" . $extensionName . "'";
		$dbInstance = $systemObject->getDbInstance();
		
		$resourceId = 0;
		
 		if ($dbInstance->issueCommand($queryString, $resourceId) == true)
		{
			// If it isnt there then it will be added upon activation
			// otherwise if it is there and is active we then load it
			
			$resultSet = $dbInstance->getResult($resourceId);
			while ($row = $resultSet->fetch(PDO::FETCH_LAZY))
			{
				$extensionInstance = clone $derivedExtension;
								
				// Set up position and location prior to loading
				// this is done regardless of active flag setting
				$extensionInstance->setDisplayPosition($row->position);
				$extensionInstance->setDisplayLocation($row->location);
				$extensionInstance->setInstance($row->instance);
				
				// Update this object with the active status etc
				if ($row->activeFlag == 1)
				{
					$extensionInstance->load();
					
					$this->m_extensionArray[$extensionName]->setActive(); // ensure that we know at least one is activated so we dont add dupes of those that shouldnt be duped
				
					$this->m_instantiatedExtensionArray[$extensionName . $extensionInstance->getInstance()] = $extensionInstance;
				}
			}
			$dbInstance->releaseResults($resourceId);
		}

		/*
		 * If its not there add it otherewise just replace it
		 */
//		$this->m_extensionArray[$extensionName] = $derivedExtension;
	}
	
	function activateExtension($extensionName, $position, $location)
	{
//		$neuteredName = $extensionName;
//		$neuteredName[strlen($extensionName)] = null;
//		error_log("Neutered: " . $neuteredName);

//		error_log("Activating: " . $extensionName . " at: " . $position . " and " . $location);

		// Update the database to show this extension is active
		if (array_key_exists($extensionName, $this->m_extensionArray))
		{
			$extension = $this->m_extensionArray[$extensionName];
			
			$extensionInstance = clone $extension;
			$extensionInstance->setDisplayLocation($location);
			$extensionInstance->setDisplayPosition($position);
			$extensionInstance->activate();
			$this->m_instantiatedExtensionArray[$extensionName . $extensionInstance->getInstance()] = $extensionInstance;
		}
		else { 
//			$data = print_r($this->m_extensionArray, true);
			error_log("cannot find it in: " . $extensionName);
		}
	}
	
	function deactivateExtension($extensionName)
	{
		$neuteredName = $extensionName;
		$neuteredName[strlen($extensionName)] = null;
		
//		error_log("Deactivating: " . $neuteredName);
		if (array_key_exists($extensionName, $this->m_instantiatedExtensionArray))
		{
			$extension = $this->m_instantiatedExtensionArray[$extensionName];
			$extension->deactivate();
			
			if ($extension->getAllowMultipleInstances() == false)
			{
				$extension->setInActive();
				// make sure if its single use then its available to be re-activated
	//			$this->m_extensionArray[$neuteredName]->setInActive();
			}
			unset($this->m_instantiatedExtensionArray[$extensionName]);
		}
		else
		{
			error_log("Unable to de-activate: " . $extensionName);
		}
	}
	
	function updateExtension($extensionName)
	{
		// Needs work
		if (array_key_exists($extensionName, $this->m_extensionArray))
		{
			$extension = $this->m_extensionArray[$extensionName];
			$extension->deactivate();
			//Now update
		}
	}
	
	function deleteExtension($extensionName)
	{
		// Needs work
		if (array_key_exists($extensionName, $this->m_extensionArray))
		{
			$extension = $this->m_extensionArray[$extensionName];
			$extension->deactivate();
		}
	}
	
	function processExtensionCommands($extensionName, $command, $paramArray)
	{
		$returnData = null;
		
//		error_log("running command: " . $command . " for: " . $extensionName);
		
		if (array_key_exists($extensionName, $this->m_instantiatedExtensionArray))
		{
			$extension = $this->m_instantiatedExtensionArray[$extensionName];
			$returnData = $extension->processCommand($command, $paramArray);
		}
		
		return $returnData;
	}
	
	/*
	 * processInternalCommand
	 * 
	 * @param extensionType - the type of extension to handle the command
	 * @param command - the command to be issued
	 * @param paramArray - any parameters required by the command
	 */
	function processInternalCommand($extensionType, $command, $paramArray)
	{
		foreach ($this->m_instantiatedExtensionArray as $index=>$object)
		{
			// We are only interested in the active objects
			if ($object->isActive() == true)
			{
				if ($object->getExtensionType() == $extensionType)
				{
					$object->processCommand($command, $paramArray);
				}
			}
		}
	}
	
	function preDisplay($paramArray)
	{
		foreach ($this->m_instantiatedExtensionArray as $index=>$object)
		{
			if ($object->isActive() == true)
			{
				if ($paramArray != null)
				{
					$object->setParamArray($paramArray);
				}
				$object->preDisplay();
			}
		}
	}
	
	function postDisplay()
	{
		foreach ($this->m_instantiatedExtensionArray as $index=>$object)
		{
			if ($object->isActive() == true)
			{
				$object->postDisplay();
			}
		}
	}
	
	/*
	 * displayExtensions
	 * 
	 * used to display any extensions in the specified area
	 * 
	 * @param page - the page to display the extensions on
	 * @param displayPosition - pre or post for the specified area
	 * @param displayLocation - where the widget is i.e. header, body, footer
	 */
	function displayExtensions(& $page, $displayPosition, $displayLocation)
	{
		/*
		 * This array could be arranged by display location and position to speed processing
		 * or have an array for each area.
		 * 
		 */
		foreach ($this->m_instantiatedExtensionArray as $index=>$object)
		{
			// We are only interested in the active objects
			if ($object->isActive() == true)
			{				
				if ($object->getDisplayLocation() == $displayLocation)
				{
					if ($object->getDisplayPosition() == $displayPosition)
					{
//						error_log("displaying: ". $object->getName() . " pos: " . $displayPosition . " loc: " . $displayLocation);
						$object->display($page);
					}
				}
			}
		}
	}
}
?>
