<?php
/*
 * Created on Apr 15, 2007
 *
 * Author: 	daniel
 * File:   	db_object.php
 * Project: database
 * 
 * Purpose:
 * 	base class for all db objects
 * 
 */
 
 include_once("access.php");
 
 /**
  * Class: DBTableColumn
  * Purpose: to describe a table column to be used when creating tables  
  */
 class DBTableColumn
 {
 	protected $m_columnName = "none";
 	protected $m_columnType = "none";
 	protected $m_typeLength = 0;
 	protected $m_typePrecision = 0;
 	protected $m_allowNull = false;
 	protected $m_primaryKey = false;
 	protected $m_autoIncrement = false;
 	protected $m_defaultValue = "0";
 	
 	function __construct()
 	{
 		
 	}
 	
 	public function setTypePrecision($precision)
 	{
 		$this->m_typePrecision = $precision; 		
 	}
 	
 	public function getTypePrecision()
 	{
 		return $this->m_typePrecision;
 	}
 	
 	public function setColumnName($columnName)
 	{
 		$this->m_columnName = $columnName;
 	}
 	
 	public function getColumnName()
 	{
 		return $this->m_columnName;
 	}
 	
 	public function setColumnType($columnType)
 	{
 		$this->m_columnType = $columnType;
 	}
 	
 	public function getColumnType()
 	{
 		return $this->m_columnType;
 	}
 	
 	public function setTypeLength($typeLength)
 	{
 		$this->m_typeLength = $typeLength;
 	}
 	
 	public function getTypeLength()
 	{
 		return $this->m_typeLength;
 	}
 	
 	public function setAllowNull($allowNull)
 	{
 		$this->m_allowNull = $allowNull;
 	}
 	
 	public function getAllowNull()
 	{
 		return $this->m_allowNull;
 	}
 	
 	public function setDefaultValue($defaultValue)
 	{
 		$this->m_defaultValue = $defaultValue;
 	}
 	
 	public function getDefaultValue()
 	{
 		return $this->m_defaultValue;
 	}
 	
 	public function setPrimaryKey($primaryKey)
	{
		$this->m_primaryKey = $primaryKey;
	}
	
	public function getPrimaryKey()
	{
		return $this->m_primaryKey;
	}
	
	public function setAutoIncrement($autoIncrement)
	{
		$this->m_autoIncrement = $autoIncrement;
	}
	
	public function getAutoIncrement()
	{
		return $this->m_autoIncrement;
	}
 }
 
 class DBTable
 {
 	var $m_tableColumns;
 	var $m_tableName;
 	var $m_tableDescription;
 	
 	function __construct()
 	{
 		$this->DBTable();
 	}
 	
 	function DBTable()
 	{
 		$this->m_tableColumns = array();
 		$this->m_tableName = "none";
 		$this->m_tableDescription = "none";
 	}
 	
 	public function setTableName($name)
 	{
 		$this->m_tableName = $name;
 	}
 	
 	public function getTableName()
 	{
 		return $this->m_tableName;
 	}
 	
 	public function setTableDescription($description)
 	{
 		$this->m_tableDescription = $description;
 	}
 	
 	public function getTableDescription()
 	{
 		return $this->m_tableDescription;
 	}
 	
 	public function addTableColumn($name, $type, $length = 0, $precision = 0, $allowNull = false, $defaultValue = 0, $autoIncrement = false, $primaryKey = false)
 	{
 		$tableColumn = new DBTableColumn();
 		$tableColumn->setColumnName($name);
        $tableColumn->setColumnType($type);
        $tableColumn->setTypeLength($length);
        $tableColumn->setTypePrecision($precision);
        $tableColumn->setAllowNull($allowNull);
        $tableColumn->setDefaultValue($defaultValue);
        $tableColumn->setAutoIncrement($autoIncrement);
        $tableColumn->setPrimaryKey($primaryKey);
        
        $this->m_tableColumns[] = $tableColumn;
 	}
 	
 	public function createTable($dbInstance)
 	{
 		$success = $dbInstance->createTable($this->m_tableName, $this->m_tableColumns, $this->m_tableDescription);
 		
 		return $success;
 	}
 	public function getTableColumns()
 	{
 		return $this->m_tableColumns;
 	}
 }
 
 /**
  * Class: DBObject
  * Purpose: to create an object for working with a database  
  */
 class DBObject implements IDBAccess
 {
 	protected $m_databaseName;
 	protected $m_tablePrefix;
 	protected $m_userName;
 	protected $m_password;
 	protected $m_hostName;
 	protected $m_dbLink;
 	protected $m_dbResource;
 	protected $m_lastInsertId;
 	protected $m_dbResourceArray;
 	protected $m_nextDbResourceId;
	protected $m_dbType;

 	function __construct($dbType)
 	{
 		$this->m_databaseName = "none";
 		$this->m_tablePrefix = "none";
 		$this->m_userName = "none";
 		$this->m_password = "none";
 		$this->m_hostName = "none";
 		$this->m_dbLink = null;
 		$this->m_dbResource = null;
 		$this->m_lastInsertId = 0;
 		$this->m_dbResourceArray = array();
 		$this->m_nextDbResourceId = 0;
		$this->m_dbType = $dbType;
 	}
 	
 	function __destruct()
 	{
 		$this->releaseResults();
 		if ($this->m_dbLink != null)
 		{
 			$this->closeDbConnection();
 		}
 	}
	
 	protected function connectToDb()
 	{
		$results = true;
		
 		/* Called to connect to the db - needs credentials set to do it */
		if ($this->m_dbLink == null)
		{
			$connectString = $this->m_dbType . ":host=" . $this->m_hostName;
			if ($this->m_databaseName != "none")
			{
				$connectString .= ";dbname=" . $this->m_databaseName;
			}
			#$connectString .= ";user=" . $this->m_userName . ";password=" . $this->m_password;

			try
			{
				$this->m_dbLink = new PDO($connectString, $this->m_userName, $this->m_password);
			}
			catch (PDOException $e)
			{
				error_log("Failed to connect: ". $e->getMessage());
				$this->m_dbLink = null;
				$results = false;
			}
		}		
		return $results;
 	}
 	
 	protected function storeResource($resourceObj)
 	{
 		$this->m_nextDbResourceId++;
 		
// 		error_log('storing: ' . $resourceObj);
 		$this->m_dbResourceArray[$this->m_nextDbResourceId] = $resourceObj;
 		
 		return $this->m_nextDbResourceId;
 	}
 	
 	protected function getResource($resourceObjId)
 	{
 		$returnObj = null;
 		
 		if (array_key_exists($resourceObjId, $this->m_dbResourceArray) == true)
 		{
 			$returnObj = $this->m_dbResourceArray[$resourceObjId];
 			
// 			error_log('Returning: ' . $returnObj);
 		}
 		
 		return $returnObj;
 	}
 	
 	protected function releaseResource($resourceObjId)
 	{
// 		error_log('Removing: ' . $resourceObjId);
 		if (array_key_exists($resourceObjId, $this->m_dbResourceArray) == true)
 		{
 			unset($this->m_dbResourceArray[$resourceObjId]);
 		}
 	}

	/**
	 * setCredentials
	 * 
	 * Purpose:  to set the username and password to use with the db
	 *  
	 * @param userName - the name of the database user
	 * @param password - the password for the given databse user
	 */
	public function setCredentials($userName, $password)
	{
		$this->m_userName = $userName;
		$this->m_password = $password;
	}
	
	/**
	 * setHostName
	 * 
	 * Purpose:  to set the hostname of the database
	 *  
	 * @param hostName - the name of the host of the database
	 */
	public function setHostName($hostName)
	{
		$this->m_hostName = $hostName;
	}
 
	/**
	 * setTablePrefix
	 * 
	 * Purpose:  to set the prefix to be used with each table action
	 *  
	 * @param tablePrefixName - the name of the prefix to be applied to each table action
	 */
	public function setTablePrefix($tablePrefixName)
	{
		$this->m_tablePrefix = $tablePrefixName;
	}

	/**
	 * getTablePrefix
	 * 
	 * Purpose:  to get the prefix to be used with each table action
	 *  
	 * @return tablePrefixName - the name of the prefix to be applied to each table action
	 */
	public function getTablePrefix()
	{
		return $this->m_tablePrefix;
        }

	/**
	 * createDatabase
	 * 
	 * Purpose:  to create a database
	 *  
	 * @param databaseName - the name of the database to create.  Will select the database once created.
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function createDatabase($databaseName)
	{
		$result = false;
		
		if ($this->selectDatabase($databaseName) == false)
		{
			$this->closeDbConnection();
			$this->m_databaseName = "none";
			$this->connectToDb(); // connect without the dbname

			$command = "CREATE DATABASE " . $databaseName;
			
			if ($this->issueCommand($command) == true)
			{
				error_log("We sent the command ok");
				$result = $this->selectDatabase($databaseName);
			}
			else
			{
				error_log("We failed to issue the command");
			}
		}
		else
		{
			error_log("I told them we already got one...");
			$result = true; // its already there we are good.
		}
		
		return $result;
	}
	
	/**
	 * dropDatabase
	 * 
	 * Purpose:  to drop/delete a database
	 *  
	 * @param databaseName - the name of the database to drop.
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function dropDatabase($databaseName)
	{
		$result = false;
		
		if ($this->selectDatabase($databaseName) == true)
		{
			$command = "DROP DATABASE " . $databaseName;
			$result = $this->issueCommand($command);

			$this->m_databaseName = "none";
		}
		else
		{
			$result = true;
		}
		return $result;
	}

	/**
	 * selectDatabase
	 * 
	 * Purpose:  to select the database to be used for all commands issued
	 *  
	 * @param databaseName - the name of the database to be selected.
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function selectDatabase($databaseName)
	{
		$result = false;

		if ($this->m_databaseName != $databaseName)
		{
			// if it exists then we can proceed
			if ($this->doesDatabaseExist($databaseName) == true)
			{
				$this->closeDbConnection();
				$this->m_databaseName = $databaseName;
				$this->connectToDb();
				$result = true;
			}
		}
		else
		{
			$result = true; // already selected
		}
		return $result;
	}

	 public function doesDatabaseExist($databaseName)
	 {
		 $haveDB = false;

		 if ($this->connectToDb() == true)
		 {
			$cdResourceId = 0;
			$command = "select SCHEMA_NAME from information_schema.SCHEMATA;";
			$this->issueCommand($command, $cdResourceId);
			$rowResult = $this->getResult($cdResourceId);

			if ($rowResult->rowCount() > 0)
			{
				foreach ($rowResult as $row)
				{
					if ($row[0] == $databaseName)
					{
						$haveDB = true;
						break;
					}
				}
			}
		 
		 }
		 return $haveDB;
	 }

        /**
         * getSelectedDatabase
         *
         * Purpose:  to return the currently selected database
         *
         * @return string with the name of the database selected if any
         */
        public function getSelectedDatabase()
        {
          return $this->m_databaseName;
        }


	/**
	 * createTable
	 * 
	 * Purpose:  to create a table in the selected database.  The table prefix name will be applied.
	 *  
	 * @param tableName - the name of the table to be created without the prefix
	 * @param tableColumns - reference to table columns to be created
	 * @param userComment - users comment regarding the table being created
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function createTable($tableName, & $tableColumns, $userComment)
	{
		$result = false;
                $addComma = false;
		$command = "CREATE TABLE " . $this->m_tablePrefix . $tableName . " ( ";
		
		foreach ($tableColumns as $column)
		{
            if ($addComma == true)
            {
              $command .= ", ";
            }
			$command .= $column->getColumnName() . " " . $column->getColumnType();
			if ($column->getTypeLength() > 0)
			{
				$command .= " (" . $column->getTypeLength() . ") ";
			}
					
			if ($column->getAllowNull() == false)
			{
				$command .= " NOT NULL";
			}
			
			if ($column->getAutoIncrement() == true)
			{
				$command .= " AUTO_INCREMENT ";
			}
			else if ($column->getAllowNull() == true)
			{
				$command .= " NULL default " . $column->getDefaultValue();
			}
			if ($column->getPrimaryKey() == true)
			{
			  $command .= "PRIMARY KEY ";
			}
            $addComma = true;
		}
		$command .= ") COMMENT='" . $userComment . "'";
		
error_log("Creating tableX: " . $command);
		$result = $this->issueCommand($command);
		
		return $result;
	}

	/**
	 * doesTableExist
	 *
	 * Purpose: to query the db to see if the specified table exists
	 *
	 * @param tableName - the name of the table without the prefix
	 *
	 * @return bool true indicating existance, false otherwise
	 */
	public function doesTableExist($tableName)
	{
          return false;
	}

	/**
	 * dropTable
	 * 
	 * Purpose:  to drop a table in the selected database.  The table prefix name will be applied.
	 *  
	 * @param tableName - the name of the table to be created without the prefix
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function dropTable($tableName)
	{
		$result = false;
		$command = "drop table " . $this->m_tablePrefix . $tableName;
		
		$result = $this->issueCommand($command);
		
		return $result;
	}

	/**
     * createStoredProcedure
     *
     * Purpose:  to create a stored procedure to be called
     *
     * @param procedureName - the name of the procedure to be created without the prefix
     * @param inParameters - all of the parameters declared as IN
     * @param outParameters - all of the parameters declared as OUT
     * @param procedureData - the actual procedure call
     * 
     * @return bool true indicating success, false otherwise
     */
	public function createStoredProcedure($procedureName, $inParameters,
		$outParameters, $procedureData)
	{
		$result = false;
		$command = "drop procedure if exists `" . $this->m_databaseName . "`.`" . $this->m_tablePrefix . $procedureName . "`";

		$result = $this->issueCommand($command);

		$command = "create procedure `" . $this->m_databaseName . "`.`" . $this->m_tablePrefix . $procedureName . "` (";
		$paramCount = 0;
		foreach ($inParameters as $key => $value)
		{
			$paramCount++;
			$command .= "\n  IN " . $key . "  " . $value;
			if ($paramCount < count($inParameters))
			{
				$command .= ", ";
			}
			elseif (count($outParameters) != 0)
			{
				$command .= ", ";
			}
		}

		$paramCount = 0;
		foreach ($outParameters as $key => $value)
		{
			$paramCount++;
			$command .= "\n  OUT " . $key . "  " . $value;
			if ($paramCount < count($outParameters))
			{
				$command .= ", ";
			}
		}

		$command .= "\n )" . $procedureData . "\n ";

		$result = $this->issueCommand($command);

		return $result;
	}

	/**
	 * issueCommand
	 * 
	 * Purpose:  to isssue a command to the database using the currently selected database
	 *  
	 * @param command - the command to be issued.
	 * 
	 * @return bool indicating success, false otherwise
	 */
	public function issueCommand($command, & $resourceId = null)
	{
		$result = false;
		
		if ($this->connectToDb() == true)
		{
			$this->m_dbResource = $this->m_dbLink->query($command);
			if ($this->m_dbLink->errorCode() != 0)
			{
				error_log("Command: " . $command);
				$arr = $this->m_dbLink->errorInfo();
				error_log(print_r($arr, true));
				$this->m_dbResource = null;
                $result = false;
			}
			else
			{
				if ($this->m_dbResource === TRUE)
				{
					$this->m_lastInsertId = 0;
				}
				else
				{
					$this->m_lastInsertId = $this->m_dbLink->lastInsertId();
					if ($resourceId !== null)
					{
						$resourceId = $this->storeResource($this->m_dbResource);
					}
				}
				$result = true;
			}
		}
		else
		{
			error_log("Cannot connect to the db...");
		}
		return $result;
	}

	/**
	 * getLastInsertId
	 * 
	 * Purpose:  for commands that insert new data into the database, this will return the last id associated with said command
	 * 
	 * @return int indicating the id of the last insert command
	 */
	public function getLastInsertId()
	{
		return $this->m_lastInsertId;
	}

	/**
	 * getResult
	 * 
	 * Purpose:  to get a result object from the last db result set
	 *  
	 * @return a stdClass object representing the database row
	 *         returns FALSE when there are no more results to get
	 */
	public function getResult($resourceId = null)
	{
		$resourceObj = null;
		
		if ($resourceId != null)
		{
			$resourceObj = $this->getResource($resourceId);
		}
		else
		{
			$resourceObj = $this->m_dbResource;
		}
		
		return $resourceObj;
	}

	/**
	 * releaseResults
	 * 
	 * Purpose:  to release any results of a command that was issued
	 *  
	 */
	public function releaseResults($resourceId = null)
	{
		$resourceObj = null;
		
		if ($resourceId != null)
		{
			$resourceObj = $this->getResource($resourceId);
		}
		else
		{
			$resourceObj = $this->m_dbResource;
		}
		
		if (($resourceObj != null) && ($resourceObj !== TRUE))
		{
			$resourceObj->closeCursor();
			
			if ($resourceId != null)
			{
				$this->releaseResource($resourceId);
			}
			else
			{
				$this->m_dbResource = null;
			}
		}	
	}

	/**
	 * closeDbConnection
	 * 
	 * Purpose:  to release any open connection
	 *  
	 */
	public function closeDbConnection()
	{
		$this->m_dbLink = null;
	}
 } 
 
?>
