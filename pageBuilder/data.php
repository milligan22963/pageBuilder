<?php
/*
 * data.php
 * 
 * Used to manage data associated with this extension.
 */

$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/xmlPage.php';
include_once $baseSiteDir . 'pageBuilder/jsonPage.php';
include_once $baseSiteDir . 'pageBuilder/svgPage.php';
include_once $baseSiteDir . 'pageBuilder/paramManager.php';

//include_once 'xmlPage.php';
//include_once 'paramManager.php';

define('NEW_DATABASE_ITEM', 0);

define('COMMAND_VIEW', "view");
define('COMMAND_CREATE', "create");
define('COMMAND_MODIFY', "modify");
define('COMMAND_DELETE', "delete");
define('COMMAND_ACTIVATE', "activate");
define('COMMAND_TAG', "tag");
define('RESULT_ARRAY_INDEX', "results");
define('SUCCESS_ARRAY_INDEX', "success");

class Data extends ParamManager
{
	private $m_xmlFormat;
	private $m_id;
	private $m_isActive;
	private $m_timeStamp;
	private $m_tableName;
	private $m_tablePrefix;
		
	function __construct()
	{
		parent::__construct();
		$this->reset();
		
		// load my params
		$fileBase = dirname(__FILE__);
		$this->loadCommandFile($fileBase . '/commandparam.xml');
	}
	
	function reset()
	{
		$this->m_id = NEW_DATABASE_ITEM;
		$this->m_isActive = false;
		$this->m_timeStamp = null;
		$this->m_tableName = null;
		$this->m_xmlFormat = false;

		$systemObject = getSystemObject();
		$this->m_tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
	}

	function getName()
	{
		return "none";
	}
	
	function setId($id)
	{
		$this->m_id = $id;
	}
	
	function getId()
	{
		return $this->m_id;
	}
	
	function setActive($isActive)
	{
		$this->m_isActive = $isActive;
	}
	
	function getActive()
	{
		return $this->m_isActive;
	}
	
	function setTimeStamp($timeStamp)
	{
		$this->m_timeStamp = $timeStamp;
	}
	
	function getTimeStamp()
	{
		return $this->m_timeStamp;
	}
	
	function setTableName($tableName)
	{
		$this->m_tableName = $tableName;
	}
	
	function getTableName()
	{
		return $this->m_tableName;
	}

	function getFullTableName()
	{
		return $this->m_tablePrefix . $this->getTableName();
	}
	
	function loadData($id)
	{
		$this->setId($id);
		
		// DWM currently we are not doing much more than this as we don't want to have mutliple calls to the db
		// we will need update a db request using class level methods and do the same on saving
	}
	
	function saveData()
	{
		//error_log("Saving data");
	}
	
	/*
	 * getLastInsertId
	 * 
	 * used to get the last id inserted into a table for this extension assuming each table has a timeStamp and id column
	 * 
	 * @return integer indicating the last id of the table or 0 for none
	 */
	function getLastInsertId()
	{
		$retVal = 0;
		$systemObject = getSystemObject();
		$dbInstance = $systemObject->getDbInstance();
		
		$queryString = "select id, timeStamp from " . $this->getFullTableName() . " order by timeStamp desc limit 1";
		$queryId = 0;
		$dbInstance->issueCommand($queryString, $queryId);
		$resultSet = $dbInstance->getResult($queryId);
		if ($resultSet != null)
		{
			if ($resultSet->rowCount() > 0)
			{
				$row = $resultSet->fetch(PDO::FETCH_LAZY);
				if ($row != null)
				{
					$retVal = $row->id;
				}
			}
		}
		$dbInstance->releaseResults($queryId);
		return $retVal;
	}
	
	function toXml($name = "data")
	{
		$node = new XmlDataObject();
		
		$node->setName($name);
		
		$node->addAttribute("id", $this->getId());
		$node->addAttribute("active", $this->getActive());
		$node->addAttribute("timeStamp", $this->getTimeStamp());
		
		return $node;
	}
	
	function toJSON($name = "data")
	{
		$json = new JSONDataObject();
		//$json = new JSONArrayObject();
		
		$json->setName($name);
		
		// add attribute
		$arrayLine = new JSONDataObject();
		$arrayLine->setName("id");
		$arrayLine->setValue($this->getId());
		$json->addChildObject($arrayLine);

		$arrayLine = new JSONDataObject();
		$arrayLine->setName("active");
		if ($this->getActive() == true)
		{
			$arrayLine->setValue("true");
		}
		else
		{
			$arrayLine->setValue("false");			
		}
		$json->addChildObject($arrayLine);
		
		$arrayLine = new JSONDataObject();
		$arrayLine->setName("timeStamp");
		$arrayLine->setValue($this->getTimeStamp());

		$json->addChildObject($arrayLine);
		
		return $json;
	}
	
	function toSVG($name = "svg")
	{
		$svg = new SVGDataObject();
		
		$svg->setName($name);
		
		// nothing to add here
		
		return $svg;
	}	
	
	function fromSql($resultSet)
	{
		$this->setId($resultSet->id);
		if ($resultSet->activeFlag == 1)
		{
			$this->setActive(true);
		}
		else
		{
			$this->setActive(false);
		}
		
		$this->setTimeStamp($resultSet->timeStamp);
	}
	
	/*
     * getCommandResults
     */
    function getCommandResults($success)
    {
	    $results = null;
	    $childData = null;
	    
	    if ($this->m_xmlFormat == true)
	    {
	        $results = new XmlPageData();
	        $childData = $this->toXml("response");
		}
		else // json format
		{
			$results = new JSONPageData();
			$childData = $this->toJSON("response");
		}
		
		$results->setDirectDisplay(false);
        $results->setName($this->getName());
        if ($success == true)
        {
            $results->addChild("results", "1");
        }
        else
        {
            $results->addChild("results", "0");
        }
        
        $results->addChildObject($childData);
		
        return $results;
    }

	function createData($paramArray)
	{
		$returnData = array();

		if ($this->validateParams(COMMAND_CREATE, $paramArray) == true)
		{
			// get default "ok" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(true);
			$returnData[SUCCESS_ARRAY_INDEX] = true;
		}
		else
		{
			// get default "fail" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(false);
			$returnData[SUCCESS_ARRAY_INDEX] = false;
		}

		return $returnData;		
	}

	function modifyData($paramArray)
	{
		$returnData = array();

		if ($this->validateParams(COMMAND_MODIFY, $paramArray) == true)
		{
			// get default "ok" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(true);
			$returnData[SUCCESS_ARRAY_INDEX] = true;
		}
		else
		{
			// get default "fail" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(false);
			$returnData[SUCCESS_ARRAY_INDEX] = false;
		}

		return $returnData;		
	}

	function deleteData($paramArray)
	{
		$returnData = array();

		if ($this->validateParams(COMMAND_DELETE, $paramArray) == true)
		{
			$this->loadData($paramArray['id']);

			// ensure they are the right person			
			if ($this->getUserId() == $paramArray['userId'])
			{
				$this->setActive(false);
				$this->saveData();
				
				// get default "ok" out of base class
				$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(true);
				$returnData[SUCCESS_ARRAY_INDEX] = true;
			}
			else
			{
				// get default "fail" out of base class
				$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(false);
				$returnData[SUCCESS_ARRAY_INDEX] = false;
			}
		}
		else
		{
			// get default "fail" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(false);
			$returnData[SUCCESS_ARRAY_INDEX] = false;
		}

		return $returnData;	
	}

	function activateData($paramArray)
	{
		$returnData = array();

		if ($this->validateParams(COMMAND_ACTIVATE, $paramArray) == true)
		{
			// get default "ok" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(true);
			$returnData[SUCCESS_ARRAY_INDEX] = true;
		}
		else
		{
			// get default "fail" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(false);
			$returnData[SUCCESS_ARRAY_INDEX] = false;
		}

		return $returnData;		
	}
	
	/*
	 * default view command
	 *
	 * @return array with two keys, results, success
	 */
	function viewData($paramArray)
	{
		$returnData = array();

		if ($this->validateParams(COMMAND_VIEW, $paramArray) == true)
		{
			// get default "ok" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(true);
			$returnData[SUCCESS_ARRAY_INDEX] = true;
		}
		else
		{			
			// get default "fail" out of base class
			$returnData[RESULT_ARRAY_INDEX] = $this->getCommandResults(false);
			$returnData[SUCCESS_ARRAY_INDEX] = false;
		}

		return $returnData;		
	}

	function createDefaultData($dbInstance)
	{
		return true;
	}
	
	function createTable($dbInstance)
	{
		return false;
	}
}
?>
