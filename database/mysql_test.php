<?php
/*
 * Created on Apr 16, 2007
 *
 * Author: 	daniel
 * File:   	mysql_test.php
 * Project: database
 * 
 * Purpose:
 *   To test out mysql interface access methods
 */
 
 include_once("config.php");
 include_once("mysql_access.php");
 
 $g_configArray = array();
 
 DB_registerComponentConfiguration($g_configArray);
 
 $dbInstance = new mysqlDBObject();
 
 if ($dbInstance != null)
 {
 	$dbInstance->setCredentials($g_configArray[DB_USER_NAME], $g_configArray[DB_PASSWORD]);
 	$dbInstance->setHostName($g_configArray[DB_HOST_NAME]);
 	$dbInstance->setTablePrefix($g_configArray[DB_TABLE_PREFIX]);
 	
 	
 	if ($dbInstance->createDatabase("afmGallery") == true)
 	{
 		$tableColumns = array();
 		$tableColumns[0] = new DBTableColumn();
 		$tableColumns[0]->setColumnName("id");
 		$tableColumns[0]->setColumnType("int");
 		$tableColumns[0]->setTypeLength(11);
 		$tableColumns[0]->setAutoIncrement(true);
 		$tableColumns[0]->setPrimaryKey(true);
 		$tableColumns[1] = new DBTableColumn();
 		$tableColumns[1]->setColumnName("userName");
 		$tableColumns[1]->setColumnType("varchar");
 		$tableColumns[1]->setTypeLength(64);
 		$tableColumns[1]->setAllowNull(true);
 		$tableColumns[1]->setDefaultValue(" ");
 		
 		$dbInstance->createTable("user", $tableColumns, "The table to contain users");
 	}
 }
?>
