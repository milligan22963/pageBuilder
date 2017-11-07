<?php
$baseDir = dirname(__FILE__);
include_once $baseDir . '/toolbox/usertools.php';


class UserSession
{
	private $m_currentUser;
	private $m_loggedIn;
	private static $m_singleInstance = null;
	
	private function __construct()
	{
		$this->m_currentUser = new User();
		$this->m_loggedIn = false;
		session_start();
	}
	
	public static function getInstance()
	{
		if (self::$m_singleInstance == null)
		{
			self::$m_singleInstance = new UserSession();
		}
		
		return self::$m_singleInstance;
	}
	
	function getUserId()
	{
		$returnVal = 0; // db starts with 1

		if ($this->m_loggedIn == true)
		{
			$returnVal = $this->m_currentUser->getUserId();
		}
		return $returnVal;
	}
	
	function getUserName()
	{
		$returnString = null;

		if ($this->m_loggedIn == true)
		{
			$returnString = $this->m_currentUser->getUserName();
		}
		
		return $returnString;
	}
	
	function getUserType()
	{
		$returnString = USER_TYPE_OTHER;
		
		if ($this->m_loggedIn == true)
		{
			$returnString = $this->m_currentUser->getUserType();
		}

		return $returnString;
	}
	
	function loginUser($userName, $userPassword)
	{
		$this->m_loggedIn = false;
		if ($this->m_currentUser->loadUserByUserName($userName) == true)
		{
			if ($this->m_currentUser->getUserActive() == true)
			{
				if (User::validatePassword($userName, $userPassword) == true)
				{
					$_SESSION['userName'] = $userName;
					$_SESSION['userPassword'] = $userPassword; // DWM do we want/need this?  We could assume if the userName is correct that its good.
					$_SESSION['loginTime'] = time();
					setcookie('userName', User::generateUserName($userName),
						$_SESSION['loginTime'] + getSystemObject()->getConfigurationData(SITE_SESSION_LENGTH), "/");
					//					session_write_close();
						
					$this->m_loggedIn = true;
				}
				else
				{
					error_log('User: ' . $userName . ' password fail');
				}
			}
			else
			{
				error_log('User: ' . $userName . "isn't active.");
			}
		}
		else
		{
			error_log('Cannot find user: ' . $userName);
		}
		return $this->m_loggedIn;
	}
	
	function isActive()
	{
		return $this->m_currentUser->getUserActive();
	}
	
	function isLoggedIn()
	{
		return $this->m_loggedIn;		
	}
	
	function logoutUser()
	{
		$logSystem = LogToFile::getInstance();
		if (array_key_exists('userName', $_SESSION))
		{
			$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging out user with userName:' . $_SESSION['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
		}
		else
		{
			$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging out user anonymous (' . __LINE__ . ')' . PHP_EOL);
		}
		$this->m_loggedIn = false;
		$this->m_currentUser = new User();
		unset($_SESSION['userName']);
		unset($_SESSION['userPassword']);
		unset($_SESSION['loginTime']);		
		unset($_COOKIE['userName']);
//		session_write_close();
	}
	
	function updateActivity()
	{
		if ($this->m_loggedIn == true)
		{
			if ($this->m_currentUser->getUserValid() == true)
			{
				$this->m_currentUser->updateUserLastActivity();
			}
		}
	}
}

function processUserLogin()
{
	$success = false;
	
	/*
	 * Get a user session instance which will start the session
	 */
	$loginInstance = UserSession::getInstance();

	$logSystem = LogToFile::getInstance();
	
	/*
	 * Are they current logging in?  if so get the variables posted
	 */
	if (isset($_POST['userName']) && isset($_POST['userPassword']))
//	if (isset($_GET['userName']) && isset($_GET['userPassword']))
	{
		$loginInstance->loginUser($_POST['userName'], $_POST['userPassword']);
		$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $_POST['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
		//		$loginInstance->loginUser($_GET['userName'], $_GET['userPassword']);
		$loginInstance->updateActivity();
		$success = true;
	}
	elseif (isset($_SESSION['userName']) && isset($_SESSION['userPassword']))
	{
		$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $_SESSION['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
		/*
		 * See if the session has expired
		 */
		if (isset($_SESSION['loginTime']))
		{
			$currentTime = time();
			$loginDelta = getSystemObject()->getConfigurationData(SITE_SESSION_LENGTH);
			$lastLogin = $_SESSION['loginTime'];
			
			if ($currentTime - $lastLogin < $loginDelta)
			{
				/*
				 * They are still good - update there last login to the current time
				 * 
				 * DWM if they steal the userName hash and sessionId then they can impersonate me
				 * what to do to prevent/deter this?  Hash userName with date/time and update on each access? i.e. rotating key/pair
				 */
				
				if (isset($_COOKIE['userName']))
				{
					if ($_COOKIE['userName'] == User::generateUserName($_SESSION['userName']))
					{
						$loginInstance->loginUser($_SESSION['userName'], $_SESSION['userPassword']);
						$loginInstance->updateActivity();
						$success = true;
						$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $_SESSION['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
					}
				}
				else
				{
					//if cookies are not set then revalidate with session data
					$loginInstance->loginUser($_SESSION['userName'], $_SESSION['userPassword']);
					$loginInstance->updateActivity();
					$success = true;
					$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $_SESSION['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
				}
			}
			else
			{
				$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $_SESSION['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
			}
		}
		else
		{
			$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $_SESSION['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
		}
	}
	
	if ($success == false)
	{
		$loginInstance->logoutUser();
	}
}
?>