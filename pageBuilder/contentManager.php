<?php
 
/*
 * Content
 * 
 * used to define a site page and not the actually rendered page which is handled by Page()
 * this is used to generate blog like pages and other dynamic items
 * 
 */

class Content
{
	var $m_title;
	var $m_active;
	var $m_id;
	var $m_content; /*<! array of content items for this page */
	
	function __construct()
	{
		$this->Content();
	}
	
	function Content()
	{
		$this->m_title = "New";
		$this->m_active = false;
		$this->m_id = 0;
		$this->m_content = null;
	}
	
	function setTitle($title)
	{
		$this->m_title = $title;
	}
	
	function getTitle()
	{
		return $this->m_title;
	}
	
	function activate()
	{
		$this->m_active = true;
	}
	
	function isActive()
	{
		return $this->m_active;
	}
	
	function setId($id)
	{
		$this->m_id = $id;
	}
	
	function getId()
	{
		return $this->m_id;
	}
	
	/*
	 * loadContent
	 * 
	 * called to load all of the content for the given page
	 * 
	 */
	function loadContent()
	{
		$this->m_content = array();	
		
		$systemObject = getSystemObject();
		$dbInstance = $systemObject->getDbInstance();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$queryString = "select *, cast(`active` as unsigned integer) as `activeFlag` from " . $tablePrefix . "posts where contentId=" . $this->m_id . ";";
		// Pull content from the database for this page
		if ($dbInstance->issueCommand($queryString))
		{
			$resultSet = $dbInstance->getResult();
			while ($resultSet != FALSE)
			{
				if ($resultSet->activeFlag == true)
				{
					$this->m_content[$resultSet->id] = $resultSet->data;
				}
				$resultSet = $dbInstance->getResult();
			}
			$dbInstance->releaseResults();
		}
	}
	
	function render(& $page)
	{
		// Update the title if set and if not then just add it
		$titleObjects = $page->getDisplayObjects(TITLE_DATA);
		$title = $this->m_title;
		if (count($titleObjects) > 0)
		{
			$title .= " | "; // should be configurable - perhaps create theme settings for this i.e. a theme method to call
			// such as getTitle($currentTitle, $pageTitle);
			foreach ($titleObjects as $object)
			{
				$title .= $object->getData();
			}
		}
		
		$page->setTitle($title);
		
		$bodyData = "";
		// Walk through the content and add data to the page that was passed
		foreach ($this->m_content as $id=>$data)
		{
			$bodyData .= $data;
		}
		$page->addBodyData($bodyData);
	}
}

/*
 * ContentManager
 * 
 * used to manage content that is part of the system
 * 
 */

class ContentManager
{
	static private $m_instance = null;
	var $m_pages;
	
	private function __construct()
	{
		$this->ContentManager();
	}
	
	private function ContentManager()
	{
		$this->m_pages = array();
	}
	
	static function getInstance()
	{
		if (self::$m_instance == null)
		{
			self::$m_instance = new ContentManager();
			self::$m_instance->loadContent();
		}
		
		return self::$m_instance;
	}
	
	function getContent()
	{
		return $this->m_pages;
	}
	
	function displayContent(& $page, $pageName)
	{
		if (array_key_exists($pageName, $this->m_pages))
		{
			$this->m_pages[$pageName]->loadContent();
			$this->m_pages[$pageName]->render($page);
		}
	}
	
	private function loadContent()
	{
		// Pull all of the content from the database in terms of the "pages" available
		$systemObject = getSystemObject();
		
		$dbInstance = $systemObject->getDbInstance();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$queryString = "select *, cast(`active` as unsigned integer) as `activeFlag` from " . $tablePrefix . "content;";
		if ($dbInstance->issueCommand($queryString) == true)
		{
			/* retrieve the results and populate our array */
			$resultSet = $dbInstance->getResult();
			while ($row = $resultSet->fetch(PDO::FETCH_LAZY))
			{
				if ($row->activeFlag == 1)
				{
					$content = new Content();
					$content->setId($row->id);
					$content->setTitle($row->name);
					
					$this->m_pages[$row->name] = $content;
				}
			}
			$dbInstance->releaseResults();
		}			
	}
}
?>