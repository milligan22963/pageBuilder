<?php
include_once("../configure/config.php");
include_once("../database/mysql_access.php");

# Creates stored procedures for this database

function createAddUser_SP($dbInstance)
{
  $inParameters = array();
  $outParameters = array();

  $inParameters['userName'] = "VARCHAR(128)";
  $inParameters['password'] = "VARCHAR(128)";
  $inParameters['email'] = "VARCHAR(128)";
  $tablePrefix = $dbInstance->getTablePrefix();

  $procedureData = <<<STORED_PROC_DATA
     BEGIN
       select * from {$tablePrefix}users where {$tablePrefix}users.userName = userName;
     END
STORED_PROC_DATA;

  $dbInstance->createStoredProcedure("addUser", $inParameters, $outParameters,
	$procedureData);
}

function createRegisterUser_SP($dbInstance)
{
  $inParameters = array();
  $outParameters = array();

  $inParameters['userName'] = "VARCHAR(128)";
  $inParameters['password'] = "VARCHAR(128)";
  $inParameters['email'] = "VARCHAR(128)";
  #$outParameters['results'] = "INT(4)";

  $tablePrefix = $dbInstance->getTablePrefix();

  $procedureData = <<<STORED_PROC_DATA
     BEGIN
       DECLARE success INT DEFAULT 0;
       DECLARE userId INT DEFAULT 0;

       select count({$tablePrefix}users.id) from {$tablePrefix}users where {$tablePrefix}users.userName = userName INTO success;
       if success = 0 then
         select count({$tablePrefix}email.userId) from {$tablePrefix}email where {$tablePrefix}email.emailAddress = email INTO success;

         if success = 0 then
           INSERT INTO {$tablePrefix}users (`id`, `userName`, `userPassword`,
             `userType`, `userActive`, `userTimeStamp`)
             VALUES (NULL, userName, password, 'other', b'0', CURRENT_TIMESTAMP);
           select LAST_INSERT_ID() INTO userId;
           INSERT INTO {$tablePrefix}email (`id`, `userId`, `emailAddress`,
             `emailType`, `emailActive`, `emailTimeStamp`)
             VALUES (NULL, userId, email, 'primary', b'1', CURRENT_TIMESTAMP);
           select 1 as results;
         else
           select 0 as results;
         end if;
       else
           select 0 as results;
       end if;
     END
STORED_PROC_DATA;

  $dbInstance->createStoredProcedure("registerUser", $inParameters, $outParameters,
	$procedureData);
}

function createValidateUser_SP($dbInstance)
{
  $inParameters = array();
  $outParameters = array();

  $inParameters['userName'] = "VARCHAR(128)";
  $inParameters['password'] = "VARCHAR(128)";

  $tablePrefix = $dbInstance->getTablePrefix();

  $procedureData = <<<STORED_PROC_DATA
     BEGIN
       DECLARE success INT DEFAULT 0;

       select count({$tablePrefix}users.id) from {$tablePrefix}users where {$tablePrefix}users.userName = userName and {$tablePrefix}users.userPassword = password and {$tablePrefix}users.userActive = b'1' INTO success;
       if success = 1 then
           select 1 as results;
       else
           select 0 as results;
       end if;
     END
STORED_PROC_DATA;

  $dbInstance->createStoredProcedure("loginUser", $inParameters, $outParameters,
	$procedureData);
}

function createActivateUser_SP($dbInstance)
{
  $inParameters = array();
  $outParameters = array();

  $inParameters['userName'] = "VARCHAR(128)";
  $inParameters['password'] = "VARCHAR(128)";
  $inParameters['targetUserName'] = "VARCHAR(128)";

  $tablePrefix = $dbInstance->getTablePrefix();

  $procedureData = <<<STORED_PROC_DATA
     BEGIN
       DECLARE success INT DEFAULT 0;
       DECLARE userId INT DEFAULT 0;

       select count({$tablePrefix}users.id) from {$tablePrefix}users where {$tablePrefix}users.userName = userName and {$tablePrefix}users.userPassword = password and {$tablePrefix}users.userType = 'admin' INTO success;
       if success = 1 then
           update {$tablePrefix}users SET userActive=b'1' WHERE {$tablePrefix}users.userName = targetUserName;
           select 1 as results;
       else
           select 0 as results;
       end if;
     END
STORED_PROC_DATA;

  $dbInstance->createStoredProcedure("activateUser", $inParameters, $outParameters,
	$procedureData);
}

function createDeactivateUser_SP($dbInstance)
{
  $inParameters = array();
  $outParameters = array();

  $inParameters['userName'] = "VARCHAR(128)";
  $inParameters['password'] = "VARCHAR(128)";
  $inParameters['targetUserName'] = "VARCHAR(128)";

  $tablePrefix = $dbInstance->getTablePrefix();

  $procedureData = <<<STORED_PROC_DATA
     BEGIN
       DECLARE success INT DEFAULT 0;
       DECLARE userId INT DEFAULT 0;

       select count({$tablePrefix}users.id) from {$tablePrefix}users where {$tablePrefix}users.userName = userName and {$tablePrefix}users.userPassword = password and {$tablePrefix}users.userType = 'admin' INTO success;
       if success = 1 then
           update {$tablePrefix}users SET userActive=b'0' WHERE {$tablePrefix}users.userName = targetUserName;
           select 1 as results;
       else
           select 0 as results;
       end if;
     END
STORED_PROC_DATA;

  $dbInstance->createStoredProcedure("activateUser", $inParameters, $outParameters,
	$procedureData);
}

function createStoredProcedures($dbInstance)
{
  createAddUser_SP($dbInstance);
  createRegisterUser_SP($dbInstance);
  createValidateUser_SP($dbInstance);
  createActivateUser_SP($dbInstance);
  createDeactivateUser_SP($dbInstance);
}
?>
