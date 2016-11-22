<?php
/*
 * Created on Apr 11, 2007
 *
 * Author: 	daniel
 * File:   	access.php
 * Project: database
 * 
 * Purpose:
 *   Used to access the database of choice.
 */
 
 /*
  * The following files must be loaded prior to loading this script
  * Files:
  *   config.php - the configuration defines for database access
  */

/**
 * Interface: IDBAccess
 * Purpose:	  defines the interface to all db objects
 * 
 */
interface IDBAccess
{
	/**
	 * setCredentials
	 * 
	 * Purpose:  to set the username and password to use with the db
	 *  
	 * @param userName - the name of the database user
	 * @param password - the password for the given databse user
	 */
	public function setCredentials($userName, $password);

	/**
	 * setHostName
	 * 
	 * Purpose:  to set the hostname of the database
	 *  
	 * @param hostName - the name of the host of the database
	 */
	public function setHostName($hostName);
	
	/**
	 * setTablePrefix
	 * 
	 * Purpose:  to set the prefix to be used with each table action
	 *  
	 * @param tablePrefixName - the name of the prefix to be applied to each table action
	 */
	public function setTablePrefix($tablePrefixName);

	/**
	 * getTablePrefix
	 * 
	 * Purpose:  to get the prefix to be used with each table action
	 *  
	 * @return tablePrefixName - the name of the prefix to be applied to each table action
	 */
        public function getTablePrefix();

	/**
	 * createDatabase
	 * 
	 * Purpose:  to create a database
	 *  
	 * @param databaseName - the name of the database to create.  Will select the database once created.
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function createDatabase($databaseName);

	/**
	 * dropDatabase
	 * 
	 * Purpose:  to drop/delete a database
	 *  
	 * @param databaseName - the name of the database to drop.
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function dropDatabase($databaseName);
	
	/**
	 * selectDatabase
	 * 
	 * Purpose:  to select the database to be used for all commands issued
	 *  
	 * @param databaseName - the name of the database to be selected.
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function selectDatabase($databaseName);

	/**
	 * doesDatabaseExist
	 *
	 * Purpose: to dtermine if a database exists
	 *
	 * @param databaseName = the name of the database to query
	 *
	 * @return bool true indicating it exists, false otherwise
	 */
	 public function doesDatabaseExist($databaseName);

	/**
	 * getSelectedDatabase
	 * 
	 * Purpose:  to return the currently selected database
	 *  
	 * @return string with the name of the database selected if any
	 */
	public function getSelectedDatabase();

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
	public function createTable($tableName, & $tableColumns, $userComment);

        /**
         * doesTableExist
         *
         * Purpose: to query the db to see if the specified table exists
         *
         * @param tableName - the name of the table without the prefix
         *
	 * @return bool true indicating existance, false otherwise
	 */
        public function doesTableExist($tableName);

	/**
	 * dropTable
	 * 
	 * Purpose:  to drop a table in the selected database.  The table prefix name will be applied.
	 *  
	 * @param tableName - the name of the table to be created without the prefix
	 * 
	 * @return bool true indicating success, false otherwise
	 */
	public function dropTable($tableName);

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
          $outParameters, $procedureData);

	/**
	 * issueCommand
	 * 
	 * Purpose:  to isssue a command to the database using the currently selected database
	 *  
	 * @param command - the command to be issued.
	 * @param resourceId - used to assign a resourceId to the result set in order to reference it later
	 * 
	 * @return bool indicating success, false otherwise
	 */
	public function issueCommand($command, & $resourceId = null);

	/**
	 * getLastInsertId
	 * 
	 * Purpose:  for commands that insert new data into the database, this will return the last id associated with said command
	 * 
	 * @return int indicating the id of the last insert command
	 */
	public function getLastInsertId();

	/**
	 * getResult
	 * 
	 * Purpose:  to get a result object from the last db result set
	 * 
	 * @param resourceId - used to assign a resourceId to the result set in order to reference it later
	 *  
	 * @return a stdClass object representing the database row
	 *         returns FALSE when there are no more results to get
	 */
		public function getResult($resourceId = null);

	/**
	 * releaseResults
	 * 
	 * Purpose:  to release any results of a command that was issued
	 * 
	 * @param resourceId - used to assign a resourceId to the result set in order to reference it later
	 */
	public function releaseResults($resourceId = null);
}

?>
