<?php
/*
 * text.php
 * 
 * Used to manage data associated with text objects.
 */
$baseSiteDir = System::getInstance()->getBaseSystemDir();

include_once $baseSiteDir . 'pageBuilder/data.php';
include_once $baseSiteDir . 'pageBuilder/font.php';

define('TEXT_TABLE', 'text');

class TextData extends Data
{
	private $m_userId;
	private $m_text;
	private $m_userKey;
	private $m_langId;
	private $m_font;
	private $m_fontDecoration;
	private $m_fontSize;
	private $m_textColor;
	private $m_textOpacity;
	private $m_textPosition;
	private $m_offsetX;
	private $m_offsetY;
		        
	function __construct()
	{
		$this->TextData();
	}
	
	function TextData()
	{
		parent::Data();
		
		$this->reset();		
	}
	
	function reset()
	{
		$this->m_userId = 0;
		$this->m_text = "";
		$this->m_userKey = null;
		$this->m_langId = "en_US";
		$this->m_textColor = '000000'; // include RGB
		$this->m_textOpacity = 100.00;
		$this->m_font = null;
		$this->m_fontSize = 12;
		$this->m_fontDecoration = "";
		$this->m_textPosition = 'middle';
		$this->m_offsetX = 0;
		$this->m_offsetY = 0;

		$systemObject = getSystemObject();
		$tablePrefix = $systemObject->getConfigurationData(SITE_TABLE_PREFIX);
		$this->setTableName($tablePrefix . TEXT_TABLE);
	}
	
	function setUserId($userId)
	{
		$this->m_userId = $userId;
	}
	
	function getUserId()
	{
		return $this->m_userId;
	}
	
	function setText($text)
	{
		$this->m_text = $text;
	}
	
	function getText()
	{
		return $this->m_text;
	}
	
	function setUserKey($userKey)
	{
		$this->m_userKey = $userKey;
	}
	
	function getUserKey()
	{
		return $this->m_userKey;
	}

	function setLangId($langId)
	{
		$this->m_langId = $langId;
	}
	
	function getLangId()
	{
		return $this->m_langId;
	}
	
	function setFontId($fontId)
	{
		if ($fontId != 0)
		{
			$this->m_font = new FontData();
			$this->m_font->loadData($fontId);
		}
		else
		{
			$this->m_font = null;
		}
	}
	
	function getFontId()
	{
		$fontId = 0;
		if ($this->m_font != null)
		{
			$fontId = $this->m_font->getId();
		}
		return $fontId;
	}
	
	function setTextColor($textColor)
	{
		$this->m_textColor = $textColor;
	}
	
	function getTextColor()
	{
		return $this->m_textColor;
	}
	
	function setTextOpacity($textOpacity)
	{
		$this->m_textOpacity = $textOpacity;
	}
	
	function getTextOpacity()
	{
		return $this->m_textOpacity;
	}

	function setFontSize($fontSize)
	{
		$this->m_fontSize = $fontSize;
	}
	
	function getFontSize()
	{
		return $this->m_fontSize;
	}
	
	function setFontDecoration($decoration)
	{
		$this->m_fontDecoration = $decoration;
	}
	
	function getFontDecoration()
	{
		return $this->m_fontDecoration;
	}
	
	function setTextPosition($textPosition)
	{
		$this->m_textPosition = $textPosition;
	}
	
	function getTextPosition()
	{
		return $this->m_textPosition;
	}
	
	function setOffsetX($offsetX)
	{
		$this->m_offsetX = $offsetX;
	}
	
	function getOffsetX()
	{
		return $this->m_offsetX;
	}
	
	function setOffsetY($offsetY)
	{
		$this->m_offsetY = $offsetY;
	}
	
	function getOffsetY()
	{
		return $this->m_offsetY;
	}
	
	function toXml($name = "text")
	{
		$parentObject = parent::toXml($name);
		
		if ($parentObject)
		{
			if ($this->m_font != null)
			{
				$fontXml = $this->m_font->toXml();
				$parentObject->addChildObject($fontXml);
			}
			$parentObject->addAttribute("userId", $this->getUserId());
			$parentObject->addAttribute("userkey", $this->getUserKey());
			$parentObject->addAttribute("langId", $this->getLangId());
    		$parentObject->addAttribute("textString", $this->getText());
    		$parentObject->addAttribute("textColor", $this->getTextColor());
    		$parentObject->addAttribute("textOpacity", $this->getTextOpacity());
			$parentObject->addAttribute("fontSize", $this->getFontSize());
			$parentObject->addAttribute("fontDecoration", $this->getFontDecoration());
			$parentObject->addAttribute("position", $this->getTextPosition());
			$parentObject->addAttribute("offsetX", $this->getOffsetX());
			$parentObject->addAttribute("offsetY", $this->getOffsetY());
		}
		
		return $parentObject;
	}
	
	function fromSql($resultSet)
	{
		parent::fromSql($resultSet);
		
		$this->setUserId($resultSet->userId);
		$this->setUserKey($resultSet->userKey);
		$this->setLangId($resultSet->langId);
		$this->setFontId($resultSet->fontId);
		$this->setText($resultSet->text);
		$this->setTextColor($resultSet->textColor);
		$this->setTextOpacity($resultSet->textOpacity);
		$this->setFontSize($resultSet->fontSize);
		$this->setFontDecoration($resultSet->fontDecoration);
		$this->setTextPosition($resultSet->position);
		$this->setOffsetX($resultSet->offsetX);
		$this->setOffsetY($resultSet->offsetY);
	}
	
	function loadData($textId)
	{
		parent::loadData($textId);
		
		// used to load the information regarding the image based on the passed in imageId
		$systemObject = getSystemObject();
		$dbInstance = $systemObject->getDbInstance();

		$queryString = "select *, cast(`active` as unsigned integer) as `activeFlag`";
		$queryString .= " from " . $this->getTableName();
		$queryString .= " where id=" . $textId;
		$resourceId = 0;
		if ($dbInstance->issueCommand($queryString, $resourceId) == true)
		{
			$resultSet = $dbInstance->getResult($resourceId);
			if ($resultSet != FALSE)
			{
				$this->fromSql($resultSet);
			}
			else
			{
				$this->reset();
			}
			$dbInstance->releaseResults($resourceId);
		}
	}
	
	function saveData()
	{
		parent::saveData();
		
		$userSession = UserSession::getInstance();
		
		if ($userSession->isLoggedIn() == true)
		{
			$userId = $userSession->getUserId();
			
			$this->setUserId($userId);
			
			$systemObject = getSystemObject();
			$dbInstance = $systemObject->getDbInstance();

			// If it is NEW_DATABASE_ITEM then this new otherwise it exists and we update
			$textId = $this->getId();

			if ($textId == NEW_DATABASE_ITEM)
			{
				$queryString = "insert into " . $this->getTableName();
				$queryString .= " (`userId`, `userKey`,";
				$queryString .= " `langId`, `fontId`,";
				$queryString .= " `fontSize`, `fontDecoration`,";
				$queryString .= " `text`, `textColor`,";
				$queryString .= " `textOpacity`, `position`,";
				$queryString .= " `offsetX`, `offsetY`,";
				$queryString .= " `active`, `timeStamp`)";
				$queryString .= " values ('" . $this->getUserId() . "', '" . $this->getUserKey();
				$queryString .= "', '" . $this->getLangId() . "', '" . $this->getFontId();
				$queryString .= "', '" . $this->getFontSize() . "', '" . $this->getFontDecoration(); 
				$queryString .= "', '" . $this->getText() . "', '" . $this->getTextColor();
				$queryString .= "', '" . $this->getTextOpacity() . "', '" . $this->getTextPosition(); 
				$queryString .= "', '" . $this->getOffsetX() . "', '" . $this->getOffsetY(); 
				$queryString .= "', b'1', NOW());";
//				error_log('Query String:' . $queryString);
				$dbInstance->issueCommand($queryString);
				$this->setId($this->getLastInsertId());
			}
			else
			{
				$activeString = $this->getActive() ? "b'1'" : "b'0'";
								
				$queryString = "update " . $this->getTableName();
				$queryString .= " set `langId`='" . $this->getLangId() . "', `fontId`='" . $this->getFontId() . "', ";
				$queryString .= "`fontSize`='" . $this->getFontSize() . "', `fontDecoration`='" . $this->getFontDecoration() . "', ";
				$queryString .= "`text`='" . $this->getText() . "', `textColor`='" . $this->getTextColor() . "', ";
				$queryString .= "`textOpacity`='" . $this->getTextOpacity() . "', `position`='" . $this->getTextPosition() . "', ";
				$queryString .= "`offsetX`='" . $this->getOffsetX() . "', `offsetY`='" . $this->getOffsetY() . "', ";
				$queryString .= "`active`=" . $activeString;
				$queryString .= " where id='" .  $textId . "'";
				$queryString .= " and userId='" . $userId . "';";
				$dbInstance->issueCommand($queryString);
//				error_log($queryString);
			}
		}		
	}
	
	static function deleteText($id)
	{
		// remove this one from the db
		$userSession = UserSession::getInstance();
		
		if ($userSession->isLoggedIn() == true)
		{
			$userId = $userSession->getUserId();
			
			$systemObject = getSystemObject();
			$dbInstance = $systemObject->getDbInstance();
			$textObject = new TextData();

			$queryString = "delete from " . $textObject->getTableName();
			$queryString .= " where id='" . $id . "'";
			$queryString .= " and userId='" . $userId . "';";
			$dbInstance->issueCommand($queryString);
		}		
	}
	
	/*
	 * getAll
	 * 
	 * used to get all of the text in the system
	 * 
	 * @param - true for active text, false for inactive
	 * @param - userKey which is the user supplied key for a set of text items
	 * 
	 * @return array of text objects or null if none
	 */
	static function getAll($active = true, $userKey = null)
	{
		$textArray = array();
		
		$userSession = UserSession::getInstance();
		
		if ($userSession->isLoggedIn() == true)
		{
			$userId = $userSession->getUserId();
			$textObject = new TextData();
			
			
			// This is a static function so for now I have supplied the tablePrefix and table name
			$systemObject = getSystemObject();
			$dbInstance = $systemObject->getDbInstance();
			
			$activeString = $active ? "b'1'" : "b'0'";
			
			$queryString = "select *, cast(`active` as unsigned integer) as `activeFlag` ";
			$queryString .= " from " . $textObject->getTableName() . " where active=" . $activeString;
			if ($userKey != null)
			{
				$queryString .= " and userKey='" . $userKey . "'";
			}
			$queryString .= " ORDER BY timeStamp ASC";
			
			$queryId = 0;
			if ($dbInstance->issueCommand($queryString, $queryId) == true)
			{
				$rowCount = 0;
				$resultSet = $dbInstance->getResult($queryId);
				while ($resultSet != FALSE)
				{
					$rowCount++;

					$textObject->fromSql($resultSet);
					
					$textArray[$resultSet->id] = $textObject;
					$resultSet = $dbInstance->getResult($queryId);
					$textObject = new TextData();
				}
				$dbInstance->releaseResults($queryId);
				error_log("Returning: " . $rowCount . " text.");
			}
		}		
		return $textArray;
	}
}
?>