<?php
/*
 * Created on Apr 14, 2010
 *
 * Author: 	daniel
 * File:   	mysqli_access.php
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
  * Class: mysqliDBObject
  * Purpose: to create an object for working with a mysql database  
  */
 class mysqliDBObject extends DBObject
 {
 	public function __construct()
	{
 		parent::__construct('mysql');	
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
		
		error_log("creating table: " . $command);
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

          $command = "show table status like '" . $this->m_tablePrefix . $tableName . "'";

          $result = $this->issueCommand($command);

          if ($result == true)
          {
            $result = false;
            $resultObject = $this->getResult();

            if ($resultObject->rowCount() > 0)
            {
				$row = $resultObject->fetch(PDO::FETCH_LAZY);
              if ($row->Name == $this->m_tablePrefix . $tableName)
              {
                $result = true;
              }
            }
            $this->releaseResults();
          }

          return $result;
        }
 }
 
?>
