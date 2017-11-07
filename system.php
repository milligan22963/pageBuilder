<?php
$baseDir = dirname(__FILE__);
include_once $baseDir . '/toolbox/settings.php'; /* class of settings objects */

/* 
 * The main system class for this site
 */

class System
{
	private $m_configArray;
	private $m_systemData;
	private $m_dbInstance;
	private $m_settingsArray;
	private static $m_instance = null;
	
	private function __construct()
	{
		$this->m_configArray = null;
		$this->m_systemData = null;
		$this->m_dbInstance = null;
		$this->m_settingsArray = null;
	}
	
	public static function getInstance()
	{
		if (self::$m_instance == null)
		{
			self::$m_instance = new System();
		}
		
		return self::$m_instance;
	}
	
	function setConfigurationArray(& $configArray)
	{
		$this->m_configArray = $configArray;
	}
	
	function getConfigurationArray()
	{
		return $this->m_configArray;
	}
	
	function setSystemDataArray(& $systemData)
	{
		$this->m_systemData = $systemData;
	}
	
	function getSystemDataArray()
	{
		return $this->m_systemData;
	}
	
	function setDbInstance(& $dbInstance)
	{
		$this->m_dbInstance = $dbInstance;
	}
	
	function getDbInstance()
	{
		return $this->m_dbInstance;
	}

	function setSettingsArray(& $settingsArray)
	{
		$this->m_settingsArray = $settingsArray;
	}
	
	function getSettingsArray()
	{
		return $this->m_settingsArray;
	}
	
	function getAdminPath($relative)
	{
		$returnString = null;
		if ($relative == true)
		{
			$returnString = $this->m_systemData[SITE_ADMIN_RELATIVE_PATH];
		}
		else
		{
			$returnString = $this->m_systemData[SITE_ADMIN_PATH];
		}
		return $returnString;
	}
	
	function getContentPath($relative)
	{
		$returnString = null;
		if ($relative == true)
		{
			$returnString = $this->m_systemData[SITE_CONTENT_RELATIVE_PATH];
		}
		else
		{
			$returnString = $this->m_systemData[SITE_CONTENT_PATH];
		}
		return $returnString;
	}
	
	function getThemePath($relative)
	{
		$returnString = null;
		if ($relative == true)
		{
			$returnString = $this->m_systemData[SITE_THEME_RELATIVE_PATH];
		}
		else
		{
			$returnString = $this->m_systemData[SITE_THEME_PATH];
		}
		return $returnString;
	}
	
	function getExtensionPath($relative)
	{
		$returnString = null;
		if ($relative == true)
		{
			$returnString = $this->m_systemData[SITE_EXTENSION_RELATIVE_PATH];
		}
		else
		{
			$returnString = $this->m_systemData[SITE_EXTENSION_PATH];
		}
		return $returnString;
	}
	
	function getUserContentPath($relative)
	{
		$returnString = null;
		if ($relative == true)
		{
			$returnString = $this->m_systemData[SITE_USER_CONTENT_RELATIVE_PATH];
		}
		else
		{
			$returnString = $this->m_systemData[SITE_USER_CONTENT_PATH];
		}
		return $returnString;
	}
	
	function setConfigurationData($dataKey, $dataValue)
	{
		if ($this->m_configArray != null)
		{
			$this->m_configArray[$dataKey] = $dataValue;
		}
	}
	
	function getConfigurationData($dataKey, $type = "string")
	{
		$returnVal = null;
		
		if ($this->m_configArray != null)
		{
			if (array_key_exists($dataKey, $this->m_configArray))
			{
				$storedValue = $this->m_configArray[$dataKey];
				
				/* Default is to leave as string */
				switch ($type)
				{
					case "bool":
					{
						if (strcasecmp($storedValue, "true") == 0)
						{
							$returnVal = true;
						}
						else
						{
							$returnVal = false;
						}
					}
					break;
					
					case "int":
					{
						$returnVal = (int)$storedValue;
					}
					break;
					
					default:
					{
						$returnVal = $storedValue;
					}
				}
			}
		}
		return $returnVal;
	}

	 /*
	  * getSiteRootServer
	  * 
	  */
	 function getSiteRootServer()
	 {
	    $rootServer = "http";
	    if (!empty($_SERVER['HTTPS']))
	    {
	      $rootServer .= "s";
	    }
	
	    $rootServer .= "://" . $_SERVER['HTTP_HOST'];
	    //$rootServer .= "://" . $_SERVER['SERVER_NAME'];
	    /* Check for non 80 ports */
	    if (!empty($_SERVER['SERVER_PORT']))
	    {
	      if ($_SERVER['SERVER_PORT'] != "80")
	      {
	        $rootServer .= ":" . $_SERVER['SERVER_PORT'];
	      }
	    }
	    return $rootServer;
	 }
	 
	 /*
	  * getSiteRootURL
	  * 
	  * Called to return the root URL for the site allowing lower level scripts the ability to reference other
	  * areas without using relativity
	  * 
	  * @return the full URL of the root of the site - requires configuration to be loaded
	  */
	 function getSiteRootURL()
	 {
	 	$siteRootURL = null;
	 	
	 	// See if the config array exists and the define we expect is there
	 	if (defined("SITE_ROOT_PATH") == true)
	 	{
	 		if (isset($this->m_configArray) == true)
	 		{
	 			$siteRootURL = $this->getSiteRootServer() . "/";
	 			$siteRootURL .= $this->getConfigurationData(SITE_ROOT_PATH);
	 		}
	 	}
	 	
	 	return $siteRootURL;
	 }
	 
	/**
	 * Called to return the base location area for this page exluding the actual script name
	 * 
	 */ 
	function getBaseScriptURL()
	{
	    $pageLocation = $this->getSiteRootServer();
	    $pageLocation .= htmlspecialchars(dirname($_SERVER['REQUEST_URI'])) . "/";
	    return $pageLocation;
	}
	
	function getScriptURL()
	{
	  $pageLocation = $this->getBaseScriptURL();
	  $pageLocation .= basename($_SERVER['SCRIPT_FILENAME']);
	
	  return $pageLocation;
	}
	
	function getBaseSystemDir()
	{
		$rootLocation = dirname(__FILE__) . "/";
		
		return $rootLocation;
	}
	
	/*
	 * loadSettings
	 * 
	 * Used to load all of the settings in the database into the settings array
	 *  
	 * @return true on success, false otherwise
	 */
	function loadSettings()
	{		
		$success = false;
		if ($this->m_settingsArray != null)
		{
			$success = true;
		}
		else
		{
			$tablePrefix = $this->getConfigurationData(SITE_TABLE_PREFIX);
			$dbInstance = $this->getDbInstance();
			
			$this->m_settingsArray = array();
			$queryString = "select *, cast(`active` as unsigned integer) as `active_flag` from " . $tablePrefix . "site;";
			if ($dbInstance->issueCommand($queryString) == true)
			{
				/* retrieve the results and populate our array */
				$resultSet = $dbInstance->getResult();
				while ($row = $resultSet->fetch(PDO::FETCH_LAZY))
				{
					$setting = new Setting();
					
					$setting->setId($row->id);
					$setting->setName($row->setting);
					$setting->setValue($row->value);
					if ($row->active_flag == 1)
					{
						$setting->setActive(true);
					}
					else
					{
						$setting->setActive(false);
					}
					$setting->setTimeStamp($row->time_stamp);
					$this->m_settingsArray[$row->setting] = $setting;
				}
				$dbInstance->releaseResults();
			}
			$success = true;
		}
		return $success;
	}
	
	/*
	 * getSetting
	 * 
	 * Used to retrieve a setting from the database
	 * 
	 * @param $settingName - the setting to retrieve
	 * 
	 * @param $defaultValue - the default value if the value isn't found
	 * 
	 * @return $settingValue - the value of the setting retrieved or null if not found
	 */
	function getSetting($settingName, $defaultValue = null)
	{		
		$settingValue = null;
		
		if ($this->loadSettings())
		{	
			if (array_key_exists($settingName, $this->m_settingsArray))
			{
				$settingValue = $this->m_settingsArray[$settingName]->getValue();
			}
			elseif ($defaultValue != null)
			{
				$settingValue = $defaultValue;
			}
		}	
		return $settingValue;
	}
	
	/* isSettingActive
	 * 
	 * Determines if a setting is active or not
	 * 
	 * @param $settingName
	 * 
	 * @return true if active, false otherwise
	 */
	function isSettingActive($settingName)
	{		
		$settingActive = false;
		
		if ($this->loadSettings())
		{	
			if (array_key_exists($settingName, $this->m_settingsArray))
			{
				$settingActive = $this->m_settingsArray[$settingName]->getActive();
			}
		}	
		return $settingActive;
	}
	
	/*
	 * setSettingActive
	 * 
	 * @param $settingName - the name of the setting to activate or de-activate
	 * @param $active - the acitve/deactive flag (true active, false deactive)
	 * 
	 * @return true if active, false otherwise
	 */
	function setSettingActive($settingName, $active)
	{		
		$settingActive = false;
		
		if ($this->loadSettings())
		{
			if (array_key_exists($settingName, $this->m_settingsArray))
			{
				$tablePrefix = $this->getConfigurationData(SITE_TABLE_PREFIX);
				$dbInstance = $this->getDbInstance();
				
				/* if it doesnt exist then we might have a problem */
				$this->m_settingsArray[$settingName]->setActive($active);
				
				// Update the db
				$queryString = "update " . $tablePrefix . "site set ";
				$queryString .= "`active`=b'";
				if ($active == true)
				{
					$queryString .= "1";
				}
				else 
				{
					$queryString .= "0";
				}
				$queryString .= "' where `setting`='" . $settingName . "';";
				$dbInstance->issueCommand($queryString);
			}
		}
		
		return $settingActive;
	}
	
	/*
	 * setSetting
	 * 
	 * Used to store a setting in the database
	 * 
	 * @param $settingName - the setting to store
	 * 
	 * @param $settingValue - the value of the setting to be stored, null to remove it
	 *                        the value if other then null is ignored when de-activating
	 * 
	 * @param $active - the active value of the setting to be stored, null if just updating the value
	 * 
	 * @return true on success, false otherwise
	 */
	function setSetting($settingName, $settingValue, $active)
	{		
		$success = true;
		$dbInstance = $this->getDbInstance();
		$tablePrefix = $this->getConfigurationData(SITE_TABLE_PREFIX);
		

		if ($this->loadSettings())
		{		
			if ($settingValue != null)
			{
				$queryString = "none";

				// Add/update the db - if it exists in the array then it should be in the db
				// if not then add it - we can also verify that the id = 0 if it exists
				// Both paths will create a query string to processed
				if (array_key_exists($settingName, $this->m_settingsArray))
				{
					// Update our array
					$this->m_settingsArray[$settingName]->setValue($settingValue);
					if ($active != null)
					{
						$this->m_settingsArray[$settingName]->setActive($active);
					}	
					
					// Update the db
					
					$queryString = "update " . $tablePrefix . "site set `value`='" . $settingValue . "'";
					if ($active != null)
					{
						$queryString .= ", `active`=b'";
						if ($active == true)
						{
							$queryString .= "1";
						}
						else 
						{
							$queryString .= "0";
						}
						$queryString .= "'";
					}
					$queryString .= " where `setting`='" . $settingName . "';";
				}
				else
				{
	        		$queryString = "insert into " . $tablePrefix . "site (`setting`, `value`, `active`, `time_stamp`) ";
	        		$queryString .= "values ('" . $settingName . "', '" . $settingValue . "', b'";
					if ($active == null)
					{
						$active = false; // default to inactive
					}
					if ($active == true)
					{
						$queryString .= "1";
					}
					else 
					{
						$queryString .= "0";
					}
					$queryString .= "', CURRENT_TIMESTAMP);";
				}
				
				// If successful in adding to the db then update to get back the data that was set i.e. id and timestamp
				if ($dbInstance->issueCommand($queryString) == true)
				{
					$queryString = "select * from " . $tablePrefix . "site where `setting`='" . $settingName . "';";
					if ($dbInstance->issueCommand($queryString) == true)
					{
						/* retrieve the results and populate our array */
						$resultSet = $dbInstance->getResult();
						while ($row = $resultSet->fetch(PDO::FETCH_LAZY))
						{
							$setting = new Setting();
							
							$setting->setId($row->id);
							$setting->setName($row->setting);
							$setting->setValue($row->value);
							$setting->setActive($row->active_flag);
							$setting->setTimeStamp($row->time_stamp);
							$this->m_settingsArray[$row->setting] = $setting;
						}
						$dbInstance->releaseResults();
					}
					else {
						echo $queryString;
					}
				}
			}
			else
			{
				// Remove from the DB
				$queryString = "delete from " . $tablePrefix . "site where setting='" . $settingName . "';";
				$dbInstance->issueCommand($queryString);
				
				// Remove it from the array
				unset($this->m_settingsArray[$settingName]);
			}
		}
		else
		{
			$success = false;
		}
		
		return $success;
	}
}
?>