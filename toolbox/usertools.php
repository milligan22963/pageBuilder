<?php

include_once 'settings.php';

/* These match what is in the "users.php" file under install - changes there need
 * to be reflected here.  We need to fix this.
 */
define('USER_TYPE_ADMIN', "admin");
define('USER_TYPE_DEBUG', "debug");
define('USER_TYPE_USER', "user");
define('USER_TYPE_SUPPORT', "support");
define('USER_TYPE_OTHER', "other");

define('USER_UNKNOWN', 0); // the unknown user is the one who hasn't been created yet
define('USER_ANY', -1); // the any user is -1

/*
 * User
 * 
 * Defines a user of the system.  Static methods are provided in order to allow user
 * validation ease prior to having the user in the system.  This is not a singleton in order
 * to allow the creation of additional users i.e an admin adding a user etc.
 * 
 */
class User
{
	private $m_userId;
	private $m_userName;
	private $m_userType; /* enum('admin', 'debug', 'user', 'support', 'other')*/
	private $m_userTextPassword;
	private $m_userEncryptedPassword;
	private $m_userEmail;
	private $m_userLastActivity;
	private $m_userActive;
	private $m_userValid;
	
	function __construct()
	{
		$this->m_userActive = false;
		$this->m_userEmail = null;
		$this->m_userId = 0;
		$this->m_userName = null;
		$this->m_userType = USER_TYPE_OTHER;
		$this->m_userValid = false;
		$this->m_userLastActivity = null;
		$this->m_userTextPassword = null;
		$this->m_userEncryptedPassword = null;
	}
		
	function getUserActive()
	{
		return $this->m_userActive;
	}
	
	function setUserEmail($userEmail)
	{
		$this->m_userEmail = $userEmail;
	}
	
	function getUserEmail()
	{
		return $this->m_userEmail;
	}
	
	function getUserId()
	{
		return $this->m_userId;
	}
	
	function getUserName()
	{
		return $this->m_userName;
	}
	
	function getUserTextPassword()
	{
		return $this->m_userTextPassword;
	}
	
	function getUserEncryptedPassword()
	{
		return $this->m_userEncryptedPassword;
	}
	
	function getUserType()
	{
		return $this->m_userType;
	}
	
	function getUserLastActivity()
	{
		return $this->m_userLastActivity;
	}
	
	function updateUserLastActivity()
	{
		if ($this->getUserValid() == true)
		{
			$systemObject = getSystemObject();
			
			$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
			$dbInstance = $systemObject->getDbInstance();
			
			$queryString = "update " . $tablePrefix . "activity set `activityTimeStamp`=CURRENT_TIMESTAMP";
			$queryString .= " where `userId`=" . $this->getUserId() . ";";
			
			$dbInstance->issueCommand($queryString);
			
			$this->m_userLastActivity = time();
		}
	}
	
	function getUserValid()
	{
		return $this->m_userValid;
	}
	
	private function storeResults($resultSet)
	{
		if ($resultSet != null)
		{
			error_log("User Id is: " . $resultSet->id);
			$this->m_userId = $resultSet->id;
			$this->m_userName = $resultSet->userName;
			$this->m_userEncryptedPassword = $resultSet->userPassword;
			$this->m_userType = $resultSet->userType;
			if ($resultSet->activeFlag == 1)
			{
				$this->m_userActive = true;
			}
			else
			{
				$this->m_userActive = false;
			}
			if (isset($resultSet->address) == true)
			{
				$this->m_userEmail = $resultSet->address;
			}
			else
			{
				$this->m_userEmail = null;
			}
			$this->m_userValid = true;
		}
	}
	
	function loadUserById($dbId)
	{
		$success = false;
		$this->m_userValid = false;
		
		$systemObject = getSystemObject();
		$dbInstance = $systemObject->getDbInstance();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		
		$noEmail = false;
		if ($dbInstance->doesTableExist("email") == true)
		{
			$queryString = "select tbl1.*, cast(`userActive` as unsigned integer) as `activeFlag`, tbl2.address from ";
			$queryString .= $tablePrefix . "email tbl2 inner join ";
			$queryString .= $tablePrefix . "users tbl1 ";
			$queryString .= "where tbl1.id = " . $dbId . " and tbl2.userId = tbl1.id and tbl2.primaryAddr = b'1';";
			
			if ($dbInstance->issueCommand($queryString) == true)
			{
				$resultSet = $dbInstance->getResult();
				if ($resultSet != null)
				{
					// should be at least one if they exist
					$row = $resultSet->fetch(PDO::FETCH_LAZY);
					$this->storeResults($row);
					$dbInstance->releaseResults();
					$success = true;
				}
				else // the may not have an email address...
				{
					$noEmail = true;
				}
			}
		}
		else 
		{
			$noEmail = true;
		}
		
		if ($noEmail == true)
		{
			$queryString = "select *, cast(`userActive` as unsigned integer) as `activeFlag` from " . $tablePrefix . "users where id = " . $dbId . ";";
			if ($dbInstance->issueCommand($queryString) == true)
			{
				$resultSet = $dbInstance->getResult();
				if ($resultSet != null)
				{
					// should be at least one if they exist
					$row = $resultSet->fetch(PDO::FETCH_LAZY);
					$this->storeResults($row);
					$dbInstance->releaseResults();
					$success = true;
				}
			}
		}
		return $success;
	}

	function loadUserByUserName($userName)
	{
		$success = false;
		$this->m_userValid = false;
		
		$systemObject = getSystemObject();
		$dbInstance = $systemObject->getDbInstance();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);

		$noEmail = false;
		if ($dbInstance->doesTableExist("email") == true)
		{
			$queryString = "select tbl1.*, cast(`userActive` as unsigned integer) as `activeFlag`, tbl2.address from ";
			$queryString .= $tablePrefix . "email tbl2 inner join ";
			$queryString .= $tablePrefix . "users tbl1 ";
			$queryString .= "where tbl1.userName = '" . $userName . "' and tbl2.userId = tbl1.id and tbl2.primaryAddr = b'1';";
	
			if ($dbInstance->issueCommand($queryString) == true)
			{
				$resultSet = $dbInstance->getResult();
				if ($resultSet != null)
				{
					// should be at least one if they exist
					$row = $resultSet->fetch(PDO::FETCH_LAZY);

					if ($row != null)
					{
						$this->storeResults($row);
						$success = true;
					}
					else
					{
					 	$noEmail = true;
					}
					$dbInstance->releaseResults();
				}
				else // the may not have an email...
				{
				 	$noEmail = true;
				}
			}
		}
		else
		{
			$noEmail = true;
		}
		
		if ($noEmail == true)
		{
			$queryString = "select *, cast(`userActive` as unsigned integer) as `activeFlag` from " . $tablePrefix . "users where userName ='" . $userName . "';";
			if ($dbInstance->issueCommand($queryString) == true)
			{
				$resultSet = $dbInstance->getResult();
				if ($resultSet != null)
				{
					// should be at least one if they exist
					$row = $resultSet->fetch(PDO::FETCH_LAZY);
					$this->storeResults($row);
					$dbInstance->releaseResults();
					$success = true;
				}
			}
		}
		return $success;
	}
	
	function loadUserByEmail($emailAddress)
	{
		$success = false;
		$this->m_userValid = false;
		
		$systemObject = getSystemObject();
		$dbInstance = $systemObject->getDbInstance();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		
		if ($dbInstance->doesTableExist("email") == true)
		{
			$queryString = "select tbl1.*, cast(`userActive` as unsigned integer) as `activeFlag`, tbl2.address from ";
			$queryString .= $tablePrefix . "email tbl2 inner join ";
			$queryString .= $tablePrefix . "users tbl1 ";
			$queryString .= "where tbl2.address = '" . $emailAddress . "' and tbl2.userId = tbl1.id and tbl2.primaryAddr = b'1';";
			
			if ($dbInstance->issueCommand($queryString) == true)
			{
				$resultSet = $dbInstance->getResult();
				if ($resultSet != null)
				{
					// should be at least one if they exist
					$row = $resultSet->fetch(PDO::FETCH_LAZY);
					$this->storeResults($row);
					$dbInstance->releaseResults();
					$success = true;
				}
			}
		}
		return $success;
	}
	
	function saveUser()
	{
		// Create the user which will update password and the like if they already exist
		$userObject = User::createUser($this->m_userName, $this->m_userTextPassword, $this->m_userType, $this->m_userActive);
		if ($userObject != null)
		{
			$systemObject = getSystemObject();
			$dbInstance = $systemObject->getDbInstance();
			$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
			
			if (($userObject->getUserValid() == true) && ($this->m_userEmail != null))
			{
				$this->m_userId = $userObject->getUserId();
				
				$queryString = "select * from " . $tablePrefix . "email where address='" . $this->m_userEmail;
				$queryString .= "' and emailActive=b'1';";
				if ($dbInstance->issueCommand($queryString) == true)
				{
					// If we get back results then it already exists and is active.  This shouldn't be allowed.
					$resultSet = $dbInstance->getResult();
					$row = $resultSet->fetch(PDO::FETCH_LAZY);
					if ($row == null)
					{
						error_log("Adding in user email");
						// Add in the email account if doesn't already exist
						$queryString = "insert into " . $tablePrefix . "email (`userId`, `address`, `primaryAddr`, `emailActive`, `emailTimeStamp`)";
						$queryString .= " values ('" . $this->m_userId . "', '" . $this->m_userEmail . "', b'1', b'1', NOW());";
						$dbInstance->issueCommand($queryString);
					}
					else
					{
						error_log("Looks like we already have an email for : " . $this->m_userName);
						$this->m_userEmail = $row->address;
					}
					$dbInstance->releaseResults();
				}
			}
		}
	}

	public static function generateRegistrationKey($textUserName, $userId)
	{
		$registrationKey = null;
		
		$systemObject = System::getInstance();
		$siteHash = $systemObject->getConfigurationData(SITE_REGISTRATION_SALT);
			
		$encryptionType = $systemObject->getSetting("encryption", "MD5");
		
		switch($encryptionType)
		{
			case "MD5":
			{
				$registrationKey = md5($siteHash . $textUserName . $siteHash . $userId);
			}
			break;
			case "SHA1":
			{
				$registrationKey = sha1($siteHash . $textUserName . $siteHash . $userId);
			}
			break;
			default:
			{
				$registrationKey = $textUserName;
			}
		}
		
		return $registrationKey;
	}
	
	public static function generateUserName($textUserName)
	{
		$userName = null;
		
		$systemObject = getSystemObject();
		$siteHash = $systemObject->getConfigurationData(SITE_USERNAME_SALT);
			
		$encryptionType = $systemObject->getSetting("encryption", "MD5");
		
		switch($encryptionType)
		{
			case "MD5":
			{
				$userName = md5($siteHash . $textUserName . $siteHash);
			}
			break;
			case "SHA1":
			{
				$userName = sha1($siteHash . $textUserName . $siteHash);
			}
			break;
			default:
			{
				$userName = $textUserName;
			}
		}
		
		return $userName;
	}

	public static function generateDefaultPassword($targetLength)
	{
    	$chars = 'abcdefghijklmnopqrstuvwxyz#$@!^%&*ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$count = mb_strlen($chars);

		$result = '';
		for ($i = 0; $i < $targetLength; $i++)
		{
        	$index = rand(0, $count - 1);
			$result .= mb_substr($chars, $index, 1);
    	}
    	
    	return User::generatePassword($result);
	}
	
	public static function generatePassword($textPassword)
	{
		$password = null;
		$systemObject = getSystemObject();
		$siteHash = $systemObject->getConfigurationData(SITE_HASH_SALT);
			
		$encryptionType = $systemObject->getSetting("encryption", "MD5");
		
		switch($encryptionType)
		{
			case "MD5":
			{
				$password = md5($siteHash . $textPassword . $siteHash);
			}
			break;
			case "SHA1":
			{
				$password = sha1($siteHash . $textPassword . $siteHash);
			}
			break;
			default:
			{
				$password = $textPassword;
			}
		}
		
		return $password;
	}
	
	public static function validatePassword($userName, $textPassword)
	{
		$systemObject = getSystemObject();
		
		$dbName = $systemObject->getConfigurationData(SITE_DB_NAME);
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		
		$dbInstance = $systemObject->getDbInstance();
			
	    $success = false;
	    $tableName = $tablePrefix . "users";
		$storedPassword = User::generatePassword($textPassword);
	
	    $queryString = "select userName from " . $tableName . " where userPassword='$storedPassword';";
	    if ($dbInstance->issueCommand($queryString) == true)
	    {
			$resultSet = $dbInstance->getResult();
			if ($resultSet != FALSE)
			{
				$row = $resultSet->fetch(PDO::FETCH_LAZY);

				if ($row != null)
				{
					if ($row->userName == $userName)
					{
						$success = true;
					}
				}
				$dbInstance->releaseResults();
			}
	    }
	    return $success;
	}

	public static function activateUser($userName)
	{
		$success = false;
		
		$userObject = new User();
		$userObject->loadUserByUserName($userName);
		
		if ($userObject->getUserValid() == true)
		{
			if ($userObject->getUserActive() == false)
			{
				$systemObject = getSystemObject();
		
				$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
				$dbInstance = $systemObject->getDbInstance();

	    		$tableName = $tablePrefix . "users";

			    $queryString = "update " . $tableName . " set `userActive`=b'1' where id=" . $userObject->getUserId() . ";";
			    if ($dbInstance->issueCommand($queryString) == true)
			    {
			    	$success = true;
			    }
			}
		}
		
		return $success;
	}

	public static function deactivateUser($userName)
	{
		$success = false;
		
		$userObject = new User();
		$userObject->loadUserByUserName($userName);
		
		if ($userObject->getUserValid() == true)
		{
			if ($userObject->getUserActive() == true)
			{
				$systemObject = getSystemObject();
		
				$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
				$dbInstance = $systemObject->getDbInstance();

	    		$tableName = $tablePrefix . "users";

			    $queryString = "update " . $tableName . " set `userActive`=b'0' where id=" . $userObject->getUserId() . ";";
			    if ($dbInstance->issueCommand($queryString) == true)
			    {
			    	$success = true;
			    }
			}
		}
		
		return $success;
	}
	
	public static function setUserType($userName, $userType)
	{
		$success = false;
		
		$userObject = new User();
		$userObject->loadUserByUserName($userName);
		
		if ($userObject->getUserValid() == true)
		{
			if ($userObject->getUserType() != $userType)
			{
				$systemObject = getSystemObject();
		
				$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
				$dbInstance = $systemObject->getDbInstance();

	    		$tableName = $tablePrefix . "users";

			    $queryString = "update " . $tableName . " set `userType`='" . $userType . "' where id=" . $userObject->getUserId() . ";";
			    if ($dbInstance->issueCommand($queryString) == true)
			    {
			    	$success = true;
			    }
			}
		}
		
		return $success;
	}
	
	public static function userExists($userName)
	{
		$doesExist = false;
		
		$systemObject = getSystemObject();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$dbInstance = $systemObject->getDbInstance();
		
	    $queryString = "select userName from " . $tablePrefix . "users where userName='$userName';";
	    if ($dbInstance->issueCommand($queryString) == true)
	    {
			$resultSet = $dbInstance->getResult();
			while ($row = $resultSet->fetch(PDO::FETCH_LAZY))
			{
				if ($row->userName == $userName)
				{
					$doesExist = true;
				}
			}
			$dbInstance->releaseResults();
	    }
		
		return $doesExist;
	}
	
	public static function getUser($userName)
	{
		$userObj = null;
		
		$systemObject = getSystemObject();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$dbInstance = $systemObject->getDbInstance();
		
	    $queryString = "select userName from " . $tablePrefix . "users where userName='$userName';";
	    if ($dbInstance->issueCommand($queryString) == true)
	    {
			$resultSet = $dbInstance->getResult();
			if ($resultSet != FALSE)
			{
				// should be at least one if they exist
				$row = $resultSet->fetch(PDO::FETCH_LAZY);
				if ($row != null)
				{ 
					if ($row->userName == $userName)
					{
						$userObj = new User();
						
						$userObj->loadUserByUserName($userName);
					}
				}
				$dbInstance->releaseResults();
			}
	    }
	    
		return $userObj;
	}
	
	public static function userActive($userName)
	{
		$isActive = false;
		
		$systemObject = getSystemObject();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$dbInstance = $systemObject->getDbInstance();
		
	    $queryString = "select userActive from " . $tablePrefix . "users where userName='$userName';";
	    if ($dbInstance->issueCommand($queryString) == true)
	    {
			$resultSet = $dbInstance->getResult();
			if ($resultSet->rowCount() > 0)
			{
				// should be at least one if they exist
				$row = $resultSet->fetch(PDO::FETCH_LAZY);
				if ($row != null)
				{
					if ($row->userActive == 1)
					{
						$isActive = true;
					}
				}
				$dbInstance->releaseResults();
			}
	    }
		
		return $isActive;
	}
	
	/*
	 * createUser
	 * 
	 * called to create a user in the database
	 * 
	 * @param userName - the name of the user suc as myusername123
	 * 
	 * @param userTextPassword - the plain text version of the user's password which will
	 *                           be encrypted based on the site setting in the db
	 *                           
	 * @param userType - the type of the user i.e. admin, other, debug, user, support
	 * 
	 * @param userActive - boolean to indicate if this user is active or not, false
	 *                     if using a two step registration process
	 *                     
	 * @return User class object of data including the db id if created/found
	 */
	public static function createUser($userName, $userTextPassword, $userType, $userActive)
	{
		$systemObject = getSystemObject();
		
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$dbInstance = $systemObject->getDbInstance();
			
	    $success = false;
	    $userObject = null;
	
		$alreadyExists = User::userExists($userName);
		
	    $tableName = $tablePrefix . "users";
	    
	    $storedPassword = $userTextPassword;
	    if ($userTextPassword != null)
	    {
			$storedPassword = User::generatePassword($userTextPassword);
	    }
	    
		if ($userType == null)
		{
			$userType = "user";
		}
		
		$activeFlag = "b'0'";
		if ($userActive != null)
		{
			if ($userActive == true)
			{
				$activeFlag = "b'1'";
			}
		}
    
	    if ($alreadyExists == false)
	    {
		    $queryString = "insert into " . $tableName . " (`userName`, `userPassword`, `userType`, `userActive`, `userTimeStamp`) ";
		    $queryString .= "values ('$userName', '$storedPassword', '$userType', $activeFlag, CURRENT_TIMESTAMP);";
		    if ($dbInstance->issueCommand($queryString) == true)
		    {
		    	$success = true;
		    }
	    }
	    else
	    { // since he already exists, we are good however something may have changed so we will update
		    $queryString = "update " . $tableName . " set `userType`='". $userType . "', ";
		    $queryString .= "`userActive`=" . $activeFlag . ", `userTimeStamp`=CURRENT_TIMESTAMP";
		    if ($storedPassword != null)
		    {
			    $queryString .= ", `userPassword`='" . $storedPassword . "'";
		    }
		    $queryString .= " where `userName`='" . $userName . "';";
		    
		    if ($dbInstance->issueCommand($queryString) == true)
		    {
		    	$success = true;
		    }
	    }
	    
	    if ($success == true)
	    {
	    	$userObject = new User();
	    	
	    	$userObject->loadUserByUserName($userName);
	    	
	    	if ($alreadyExists == false)
	    	{		    	
		    	/* Also add in the activity table entry */
        		$queryString = "insert into " . $tablePrefix . "activity (`userId`, `activityTimeStamp`)";
        		$queryString .= " values (" . $userObject->getUserId() . ", CURRENT_TIMESTAMP);";
        		$dbInstance->issueCommand($queryString);
        		
        		/* 
        		 * Create their content area if they hadn't existed prior to this
        		 */
        		if ($systemObject->getConfigurationData(SITE_ALLOW_USER_CONTENT_UPLOAD, "bool") == true)
        		{
        			$userPath = "../" . $systemObject->getUserContentPath(true) . $userName;
					if (is_dir($userPath) == true)
					{
						rmdir($userPath);
					}
        			mkdir($userPath, 0775);
        		}
	    	}
	    }
		return $userObject;
	}
}
?>