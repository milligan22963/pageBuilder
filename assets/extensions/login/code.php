<?php

/* 
 * Determine root url of the system
 * to include the extension base
 * 
 * it assumes the system has loaded the configuration and other base items that are needed
 */

$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/extension.php';

class LoginExtension extends Extension
{
	private $m_hidden;
	private $m_jsFunction;
		
	function __construct()
	{
		parent::__construct();

		$this->m_hidden = false;
		$this->m_jsFunction = null;
	}
	
	function activate()
	{
		parent::activate();
		
		// activate this extension in the system
	}
	
	function deactivate()
	{
		parent::deactivate();
		
		// deactivate this extension in the system
	}
	
	function load()
	{
		parent::load();
				
		// loads the extension prior to display
		$this->registerCallback("SetLoginOptions", "setLoginOptions");
	}
	
	function setLoginOptions($paramArray)
	{
		if ($paramArray["hidden"] !== null)
		{
			$this->m_hidden = $paramArray["hidden"];
		}
		if ($paramArray["js"] !== null)
		{
			$this->m_jsFunction = $paramArray["js"];
		}
		
		// position
		// hidden
		// js
	}
	
	/*
	 * preDisplay - called to prepare anything required prior to display being called such as adding menu options dynamically etc
	 * 
	 */
	function preDisplay()
	{
	}
	
	function display(& $page)
	{
		$systemObject = System::getInstance();
		
		if (($page->getPageDomain() == PAGE_DOMAIN_USER) || ($page->getPageDomain() == PAGE_DOMAIN_ADMIN))
		{
			$this->requireScript($page, "JQUERY");

			// Add in our javascript file
			$this->requireScript($page, "login.js");
			
			$baseAddress = $systemObject->getSiteRootURL() . "/";
			
			// We need to get the main index page as we are in an extension location
			$loginString = $baseAddress . $systemObject->getConfigurationData(SITE_ADMIN_PATH) . "/index.php";
	        $registerString = $loginString . "?option=register";
	
			if ($this->m_hidden == false)
			{
				$this->ShowStandardWindow($page, $loginString, $registerString);
			}
			else
			{
				$this->ShowHiddenPopup($page, $loginString, $registerString);
			}
		}
	}
	
	function ShowStandardWindow(& $page, $loginString, $registerString)
	{
		$systemObject = System::getInstance();
		
		$userSession = UserSession::getInstance();

		parent::display($page);
						
		$baseAddress = $systemObject->getSiteRootURL() . "/";
		
        $homeString = $baseAddress . "index.php";
		
		if ($userSession->isLoggedIn() == false)
		{
			$loginData = <<<LOG_SNIPPET
			<div class="loginextension">
		  		<form id="userloginext" class="login" method="post" action="javascript:loginUser('$loginString', 'loginUserName', 'loginUserPassword');">
		  		    <label id="loginUserNameLbl">User Name:
		  			<input type="text" name="loginUserName" id="loginUserName" /></label>
		  			<label id="loginUserPasswordLbl">Password:
		  		    <input type="password" id="loginUserPassword" name="loginUserPassword"/></label>
		  		    <input id="loginUserButton" type="submit" value="Login" />
		  		</form>
LOG_SNIPPET;
			/*
			 * Only add this link if they are allowed to register new users
			 */
			if ($systemObject->getConfigurationData(SITE_ALLOW_NEW_USERS, "bool") == true)
			{
				$loginData .= <<<LOG_SNIPPET
				<hr class="extensionline"/>
		  		<a href="${registerString}">Register</a>
LOG_SNIPPET;
			}
			
			$loginData .= <<<LOG_SNIPPET
			</div>
LOG_SNIPPET;
			$page->addBodyData($loginData);
		}
		else 
		{
			$currentUserName = $userSession->getUserName();

			/*
			 * Only add this link if they are allowed to register new users
			 */
			if ($systemObject->getConfigurationData(SITE_ALLOW_NEW_USERS, "bool") == true)
			{
				$registrationData = $registerString;
			}
			else
			{
				$registrationData = "null";
			}
			
			$loginData = <<<LOG_SNIPPET
		<div class="loginextension">
	  		<form id="userloginext" class="logout" method="post" action="javascript:logoutUser('$loginString', '$registrationData');">
	  		 <label id="logoutUserName">Welcome ${currentUserName}</label>
	  		    <input type="submit" value="Logout" />
	  		</form>
	  		<hr class="extensionline" />
LOG_SNIPPET;
			if ($page->getPageDomain() == PAGE_DOMAIN_ADMIN)
			{
				$loginData .= <<<LOG_SNIPPET
	  		<a href="${homeString}?option=home">Home</a>
		</div>
LOG_SNIPPET;
			}
			else
			{
				$loginData .= <<<LOG_SNIPPET
	  		<a href="${loginString}">Options</a>
		</div>
LOG_SNIPPET;
			}
			$page->addBodyData($loginData);
		}
	}
	
	function ShowHiddenPopup(& $page, $loginString, $registerString)
	{
		$systemObject = System::getInstance();
		$userSession = UserSession::getInstance();

		$this->setShowTitle(false);
		
		parent::display($page);

		if ($userSession->isLoggedIn() == false)
		{
			$loginJS = "javascript:showLoginPopup('" . $loginString . "', 'loginpopup')";
			
	        $loginData =<<<LOGIN_DATA
	        	<a id="loginlink" href="${loginJS}">Login</a>
	            <div id="loginpopup" class="dialog hidden">
	            </div>
LOGIN_DATA;
			$page->addBodyData($loginData);		
		}
		else
		{
	        $loginData =<<<LOGIN_DATA
	        	<a id="loginlink" href="javascript:logoutUser('$loginString', null);">Logout</a>
LOGIN_DATA;
			$page->addBodyData($loginData);		
		}
	}
}

/*
 * If the extension is in the database and active then it will registered
 * otherwise the admin will need to add it and activate it
 */
?>
