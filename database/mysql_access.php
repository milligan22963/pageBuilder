<?php
/*
 * Created on Apr 15, 2007
 *
 * Author: 	daniel
 * File:   	mysql_access.php
 * Project: database
 * 
 * Purpose:
 * 	To provide access routines to a mysql database
 */
 
 include_once("db_object.php");
 
 /*
  * Defines specific to mysql
  */
 define("DB_VARCHAR", "varchar");
 define("DB_INT", "int");
 define("DB_BIT", "bit");
 define("DB_DECIMAL", "decimal");
 define("DB_TIMESTAMP", "timestamp");

 
  /**
  * Class: mysqlDBObject
  * Purpose: to create an object for working with a mysql database  
  */
 class mysqlDBObject extends DBObject
 { 	
 	protected function connectToDb()
 	{
		$results = true;
		
 		/* Called to connect to the db - needs credentials set to do it */
		
		if ($this->m_dbLink == null)
		{
			$this->m_dbLink = mysql_connect($this->m_hostName, $this->m_userName, $this->m_password);//, $this->m_databaseName);
			
			if ($this->m_dbLink == FALSE)
			{
				$this->m_dbLink = null;
				$results = false;
			}
		}
		
		return $results;
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
			$command = "CREATE DATABASE " . $databaseName;
			
			if ($this->issueCommand($command) == true)
			{
				$result = $this->selectDatabase($databaseName);
			}
		}
		else
		{
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
				
		if ($this->connectToDb() == true)
		{
			if (mysql_select_db($databaseName, $this->m_dbLink) == TRUE)
			{
                $this->m_databaseName = $databaseName;
				$result = true;
			}
		}
		return $result;
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
		$result = true;
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
				$command .= " (" . $column->getTypeLength();
				if ($column->getTypePrecision() > 0)
				{
					$command .= ", " . $column->getTypePrecision();
				}
				$command .= ") ";
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
				$command .= " NULL default ";
				if ($column->getColumnType() == DB_VARCHAR)
				{
					$command .= "'" . $column->getDefaultValue() . "'";
				}
				else
				{
					$command .= $column->getDefaultValue();
				}
			}
			if ($column->getPrimaryKey() == true)
			{
			  $command .= "PRIMARY KEY ";
			}
            $addComma = true;
		}
		$command .= ") ENGINE=MyISAM COMMENT='" . $userComment . "'";
		
//		error_log("Command: $command");
		$this->issueCommand($command);
		//error_log("Result: " . $result);
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
          $result = false;

          $command = "show table status like '" . $this->m_tablePrefix . $tableName . "'";

          if ($this->issueCommand($command) == true)
          {
            $resultObject = $this->getResult();

            if ($resultObject)
            {
              if ($resultObject->Name == $this->m_tablePrefix . $tableName)
              {
                $result = true;
              }
            }
            $this->releaseResults();
          }

          return $result;
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
		#echo "SP Command: $command";

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
			$this->m_dbResource = mysql_query($command, $this->m_dbLink);
			if (stripos($command, "insert") !== false)
			{
				$this->m_lastInsertId = mysql_insert_id($this->m_dbLink);
			}
			
			if ($this->m_dbResource == FALSE)
			{
				$this->m_dbResource = null;
			}
			else
			{
				if ($this->m_dbResource === TRUE)
				{
			//		$this->m_lastInsertId = 0;
				}
				else
				{
					if ($resourceId !== null)
					{
						$resourceId = $this->storeResource($this->m_dbResource);
					}
#					$lastDbRowInserted = mysql_query("select LAST_INSERT_ID() as id", $this->m_dbLink);
					
#					if ($lastDbRowInserted)
#					{
#	                    $rowResult = mysql_fetch_object($lastDbRowInserted);
#	                    if ($rowResult)
#	                    {
#	                        $this->m_lastInsertId = $rowResult->id;
#	                    }
#						mysql_free_result($lastDbRowInserted);
#					}
				}
				$result = true;
			}
		}
		return $result;
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
		$rowResult = FALSE;
		
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
			$rowResult = mysql_fetch_object($resourceObj);
		}
		return $rowResult;
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
//			error_log('Releasing resource:' . $resourceId . ' associated with object: ' . $resourceObj);
		}
		else
		{
			$resourceObj = $this->m_dbResource;
		}
		
		if (($resourceObj != null) && ($resourceObj !== TRUE))
		{
			// There are times when the resource is auto released by php at the end of the script execution
			// which I find can then lead to trying to release an object already released.
			// this will ensure that if that happens then we don't get the warning about it not being a resource
			if (is_resource($resourceObj))
			{
				mysql_free_result($resourceObj);
			}		
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
		if ($this->m_dbLink != null)
		{
			mysql_close($this->m_dbLink);
			$this->m_dbLink = null;
		}
	}
 } 
 
?>
