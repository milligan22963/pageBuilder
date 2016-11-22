<?php
/*
 * Created on Apr 19, 2007
 *
 * Author: 	daniel
 * File:   	psgresql_access.php
 * Project: database
 * 
 * Purpose:
 * 
 */
 
 include_once("db_object.php");
 
  /**
  * Class: psgresqlDBObject
  * Purpose: to create an object for working with a postgres database  
  */
 class psgresqlDBObject extends DBObject
 {
 	function __construct()
	{
 		parent::__construct('pgsql');	
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
		
error_log("Creating table: " . $command);
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
		$result = false;

		// query the tables in the db and then see if it is there
		//select table_name from information_schema.tables where table_schema = 'public';
		$command = "SELECT " . $this->m_tablePrefix . $tableName . " FROM " . $this->m_databaseName . ".tables where table_schema = 'public'";

		if ($this->issueCommand($command) == true)
		{
			$resultObject = $this->getResult();

			if ($resultObject)
			{
				foreach ($resultObject as $row)
				{
					if ($resultObject->Name == $this->m_tablePrefix . $tableName)
					{
						$result = true;
					}
				}
			}
			$this->releaseResults();
		}

		return $result;
	}
 }
 
?>
