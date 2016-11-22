<?php

/* 
 * Determine root url of the system
 * to include the extension base
 * 
 * it assumes the system has loaded the configuration and other base items that are needed
 */

$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/extension.php';
include_once $baseSiteDir . 'pageBuilder/menuObject.php';
include_once $baseSiteDir . 'pageBuilder/contentManager.php';

class MenuExtension extends Extension
{
	private $m_menuObjects = null;
	private $m_showPageMenu = true;
	
	function __construct()
	{
		parent::__construct();
		$this->m_menuObjects = Array();
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
		$this->registerCallback("AddMenuItem", "addMenuItem");
		$this->registerCallback("SetMenuOptions", "setMenuOptions");
	}
	
	function addMenuItem($paramArray)
	{
		//widget, title, link, class
		$widgetName = $paramArray['widget'];
		
		$menuObject = new MenuObject();
		$menuObject->setTitle($paramArray['title']);
		$menuObject->setLink($paramArray['link']);
		$menuObject->setClass($paramArray['class']);
		
		if (array_key_exists($widgetName, $this->m_menuObjects) == false)
		{
			$this->m_menuObjects[$widgetName] = Array();
		}
		array_push($this->m_menuObjects[$widgetName], $menuObject);
	}
	
	function setMenuOptions($paramArray)
	{
		if (array_key_exists('showPageMenu', $paramArray) == true)
		{
			$this->m_showPageMenu = $paramArray['showPageMenu'];
		}
		if (array_key_exists('showMenuTitle', $paramArray) == true)
		{
			$this->setShowTitle($paramArray['showMenuTitle']);
		}
	}
	
	/*
	 * preDisplay - called to prepare anything required prior to display being called such as adding menu options dynamically etc
	 * 
	 */
	function preDisplay()
	{
		parent::preDisplay();
	}
	
	/*
	 * postDisplay - called to clean up anything that might have occurred like saving settings and the like
	 * 
	 */
	function postDisplay()
	{
		parent::postDisplay();
	}
	
	function display(& $page)
	{
		$systemObject = System::getInstance();
		
		$userSession = UserSession::getInstance();
		
		$adminOnly = $systemObject->getSetting("menuAdminOnly", false);
		
		if ($page->getPageDomain() == PAGE_DOMAIN_USER && $adminOnly == false)
		{
			parent::display($page);
			
			$this->requireScript($page, "JQUERY");
	
			// Add in our javascript file
			$this->requireScript($page, "menu.js");

			$dataObjectArray = null;
			
			/*
			 * while this menu is part of the main system, the layout and look/feel is
			 * done via the theme.  I may want to remove this and just allow the theme to
			 * designate the menu but for now here it is.  Of course the theme designer
			 * is free to place the menu whereever they like
			 * 
			 * Extensions can also modify to fit their needs
			 */
			if ($this->m_showPageMenu == true)
			{
				$userMenu = new Menu();
				$userMenu->setMenuValues("menuextension", "usermenu", "usermenu", "ul");
				
				$userIndexPage = $systemObject->getSiteRootURL() . "/index.php";
	
				// Pull each active page from the db
				$contentManager = ContentManager::getInstance();
				
				$currentPage = $systemObject->getConfigurationData(SITE_CURRENT_PAGE_NAME, "string");
				
				$userPages = $contentManager->getContent(); //array('home');
				foreach ($userPages as $userPage)
				{
					$menuObject = new MenuObject();
					$menuObject->setTitle($userPage->getTitle());
					$menuObject->setLink($userIndexPage . "?option=" . $userPage->getTitle());
					$menuObject->setClass("usermenu");
					if ($userPage->getTitle() == $currentPage)
					{
						$menuObject->addAttribute("usermenuselected", "true");
					}
					$userMenu->addMenuObject($menuObject);
				}
				$dataObjectArray = $userMenu->renderMenu();
			}
						
			/* Add in menu items for other extensions */
			foreach ($this->m_menuObjects as $widgetName=>$menuArray)
			{
				$userMenu = new Menu();
				$userMenu->setMenuValues("extension" . $widgetName, "usermenu" . $widgetName, "usermenu" . $widgetName, "ul");

				if ($this->getShowTitle() == true)
				{
					$menuHeaderClass = "extensiontitle " . $widgetName;
					$userMenu->setMenuHeader($widgetName, $menuHeaderClass);
				}
								
				foreach ($menuArray as $index=>$menuObject)
				{
					$userMenu->addMenuObject($menuObject);
				}
				if ($dataObjectArray != null)
				{
					$dataObjectArray = array_merge($dataObjectArray, $userMenu->renderMenu());
				}
				else
				{
					$dataObjectArray = $userMenu->renderMenu();
				}	
			}
			
			foreach ($dataObjectArray as $dataObjectIndex=>$dataObject)
			{
				$page->addBodyData($dataObject->getData());
			}
		}
		else if ($page->getPageDomain() == PAGE_DOMAIN_ADMIN)
		{
			parent::display($page);
			
			global $g_adminOptionArray;
			if (isset($g_adminOptionArray) == true)
			{
				/*
				 * while this menu is part of the main system, the layout and look/feel is
				 * done via the theme.  I may want to remove this and just allow the theme to
				 * designate the menu but for now here it is.
				 * 
				 */
				$adminMenu = new Menu();
				$adminMenu->setMenuValues("menuextension", "adminmenu", "adminmenu", "ul");
				
				$adminIndexPage = $systemObject->getSiteRootURL() . "/" . $systemObject->getAdminPath(true) . "index.php";

				// Admin users should see an admin menu
				foreach ($g_adminOptionArray as $option=>$code)
				{
					$menuObject = new MenuObject();
					$menuObject->setTitle($option);
					$menuObject->setLink($adminIndexPage . "?option=" . $option);
					$menuObject->setClass("adminmenu");
					$adminMenu->addMenuObject($menuObject);
				}
				$dataObjectArray = $adminMenu->renderMenu();

				/* Add in menu items for other extensions */
				foreach ($this->m_menuObjects as $widgetName=>$menuArray)
				{
					$userMenu = new Menu();
					$userMenu->setMenuValues("extension" . $widgetName, "usermenu" . $widgetName, "usermenu" . $widgetName, "ul");
					$menuHeaderClass = "extensiontitle " . $widgetName;
					$userMenu->setMenuHeader($widgetName, $menuHeaderClass);
					
					foreach ($menuArray as $index=>$menuObject)
					{
						$userMenu->addMenuObject($menuObject);
					}
					$dataObjectArray = array_merge($dataObjectArray, $userMenu->renderMenu());
				}
				
				foreach ($dataObjectArray as $dataObjectIndex=>$dataObject)
				{
					$page->addBodyData($dataObject->getData());
				}
			}
		}
	}
}

/*
 * If the extension is in the database and active then it will registered
 * otherwise the admin will need to add it and activate it
 */
?>
