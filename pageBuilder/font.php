<?php
/*
 * font.php
 * 
 * Used to manage data associated with font objects.
 */
$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/data.php';

define('FONT_TABLE', 'fonts');
define('FONT_TYPE_TABLE', 'fonts_types');

class FontTypeData extends Data
{
	private $m_name;
		        
	function __construct()
	{
		parent::Data();
		
		$this->reset();		
	}
	
	function reset()
	{
		$this->m_name = "";

		$systemObject = getSystemObject();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$this->setTableName($tablePrefix . FONT_TYPE_TABLE);
	}
	
	function setName($name)
	{
		$this->m_name = $name;
	}
	
	function getName()
	{
		return $this->m_name;
	}
	
	function toXml($name = "fontType")
	{
		$parentObject = parent::toXml($name);
		
		if ($parentObject)
		{
			$parentObject->addAttribute("name", $this->getName());
		}
		
		return $parentObject;
	}
	
	function fromSql($resultSet)
	{
		parent::fromSql($resultSet);
		
		$this->setName($resultSet->name);
	}
	
	function loadData($fontTypeId)
	{
		parent::loadData($fontTypeId);
		
		$systemObject = getSystemObject();
		$dbInstance = $systemObject->getDbInstance();

		$queryString = "select *, cast(`active` as unsigned integer) as `active_flag`";
		$queryString .= " from " . $this->getTableName();
		$queryString .= " where id=" . $fontTypeId;
		$resourceId = 0;
		if ($dbInstance->issueCommand($queryString, $resourceId) == true)
		{
			$resultSet = $dbInstance->getResult($resourceId);
			if ($resultSet != FALSE)
			{
				$this->fromSql($resultSet);
			}
			else
			{
				$this->reset();
			}
			$dbInstance->releaseResults($resourceId);
		}
	}
	
	function saveData()
	{
		parent::saveData();
	}
}

class FontData extends Data
{
	private $m_type;
	private $m_name;
	private $m_secondaryName;

	function __construct()
	{
		parent::Data();

		$this->reset();
	}

	function reset()
	{
		$this->m_type = null;
		$this->m_name = "";
		$this->m_secondaryName = "";

		$systemObject = getSystemObject();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$this->setTableName($tablePrefix . FONT_TABLE);
	}
	
	function setTypeId($typeId)
	{
		if ($typeId != 0)
		{
			$this->m_type = new FontTypeData();
			$this->m_type->loadData($typeId);
		}
		else
		{
			$this->m_type = null;
		}
	}
	
	function getTypeId()
	{
		$typeId = 0;
		if ($this->m_type != null )
		{
			$typeId = $this->m_type->getId();
		}
		return $typeId;
	}
	
	function setName($name)
	{
		$this->m_name = $name;
	}
	
	function getName()
	{
		return $this->m_name;
	}

	function setSecondaryName($secondaryName)
	{
		$this->m_secondaryName = $secondaryName;
	}
	
	function getSecondaryName()
	{
		return $this->m_secondaryName;
	}
	
	function toXml($name = "font")
	{
		$parentObject = parent::toXml($name);
		
		if ($parentObject)
		{
			if ($this->m_type != null)
			{
				$typeXml = $this->m_type->toXml();
				$parentObject->addChildObject($typeXml);
			}
			$parentObject->addAttribute("name", $this->getName());
			$parentObject->addAttribute("secondaryName", $this->getSecondaryName());
		}
		
		return $parentObject;
	}
	
	function fromSql($resultSet)
	{
		parent::fromSql($resultSet);
		
		$this->setName($resultSet->name);
		$this->setSecondaryName($resultSet->secondary_name);
		$this->setTypeId($resultSet->font_type);
	}
	
	function loadData($fontId)
	{
		parent::loadData($fontId);
		
		// used to load the information regarding the image based on the passed in imageId
		$systemObject = getSystemObject();
		$dbInstance = $systemObject->getDbInstance();

		$queryString = "select *, cast(`active` as unsigned integer) as `active_flag`";
		$queryString .= " from " . $this->getTableName();
		$queryString .= " where id=" . $fontId;
		$resourceId = 0;
		if ($dbInstance->issueCommand($queryString, $resourceId) == true)
		{
			$resultSet = $dbInstance->getResult($resourceId);
			if ($resultSet != FALSE)
			{
				$this->fromSql($resultSet);
			}
			else
			{
				$this->reset();
			}
			$dbInstance->releaseResults($resourceId);
		}
	}
	
	function saveData()
	{
		parent::saveData();
		
	}
	
	/*
	 * getAll
	 * 
	 * used to get all of the fonts in the system
	 * 
	 * @param - true for active fonts, false for inactive
	 * 
	 * @return array of fonts objects or null if none
	 */
	static function getAll($active = true)
	{
		$fontArray = array();
		
		$userSession = UserSession::getInstance();
		
		if ($userSession->isLoggedIn() == true)
		{
			$userId = $userSession->getUserId();
			$fontObject = new FontData();
			
			
			// This is a static function so for now I have supplied the tablePrefix and table name
			$systemObject = getSystemObject();
			$dbInstance = $systemObject->getDbInstance();
			
			$activeString = $active ? "b'1'" : "b'0'";
			
			$queryString = "select *, cast(`active` as unsigned integer) as `active_flag` ";
			$queryString .= " from " . $fontObject->getTableName() . " where active=" . $activeString;
			$queryString .= " ORDER BY time_stamp ASC";
			
			$queryId = 0;
			if ($dbInstance->issueCommand($queryString, $queryId) == true)
			{
				$rowCount = 0;
				$resultSet = $dbInstance->getResult($queryId);
				while ($resultSet != FALSE)
				{
					$rowCount++;

					$fontObject->fromSql($resultSet);
					
					$fontArray[$resultSet->id] = $fontObject;
					$resultSet = $dbInstance->getResult($queryId);
					$fontObject = new FontData();
				}
				$dbInstance->releaseResults($queryId);
//				error_log("Returning: " . $rowCount . " fonts.");
			}
		}		
		return $fontArray;
	}
}
?>