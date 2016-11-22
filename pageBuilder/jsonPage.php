<?php
/*
 * Created on Jan 02, 2016
 *
 * Author: D.W. Milligan
 * Copyright: AFM Software 2016
 * Project: afmGallery
 * File: jsonPage.php
 */

//http://www.json.org
//http://www.jslint.com output validated briefly

define('START_ENCLOSING_CHARACTER_INDEX', 0);
define('END_ENCLOSING_CHARACTER_INDEX', 1);

class JSONDataObject
{
  var $m_name;
  var $m_value; // can have a value or a child array
  var $m_childObjects;
  var $m_nextChildIndex;
  var $m_enclosingCharacters;

  function __construct()
  {
    $this->m_name = null;
    $this->m_value = null;
    $this->m_childObjects = null;
    $this->m_nextChildIndex = 0;
    $this->m_enclosingCharacters = array(); //"{";
    $this->m_enclosingCharacters[START_ENCLOSING_CHARACTER_INDEX] = '{';
    $this->m_enclosingCharacters[END_ENCLOSING_CHARACTER_INDEX] = '}';
  }

  function setEnclosingCharacters($startChar, $endChar)
  {
    $this->m_enclosingCharacters[START_ENCLOSING_CHARACTER_INDEX] = $startChar;
    $this->m_enclosingCharacters[END_ENCLOSING_CHARACTER_INDEX] = $endChar;
  }
  
  function getEnclosingCharacter($characterType)
  {
	return $this->m_enclosingCharacters[$characterType];  
  }
  
  function renderObject()
  {
	$encloseIt = true;
	$renderedText = '';
	  
	if ($this->m_name !== null)
	{
	  $renderedText .= '"' . $this->m_name . '"';
	}
	else
	{
		$encloseIt = false;
	}
	
	if ($this->m_value !== null)
	{
		$renderedText .= ':"' . $this->m_value . '"';
	}
	else if ($this->m_childObjects != null)
	{
		if ($encloseIt == true)
		{
			$renderedText .= ":";
			$renderedText .= $this->m_enclosingCharacters[START_ENCLOSING_CHARACTER_INDEX];
		}
		for ($childIndex = 0; $childIndex < $this->m_nextChildIndex; $childIndex++)
		{
			if ($childIndex > 0)
			{
				$renderedText .= ",";
			}
			$renderedText .= $this->m_childObjects[$childIndex]->renderObject();
    	}
    	if ($encloseIt == true)
    	{
			$renderedText .= $this->m_enclosingCharacters[END_ENCLOSING_CHARACTER_INDEX];
		}
	}
		  	
    return $renderedText;
  }

  function setName($name)
  {
    $this->m_name = $name;
  }

  function getName()
  {
    return $this->m_name;
  }

  function setValue($value)
  {
    $this->m_value = $value;
  }

  function getValue()
  {
    return $this->m_value;
  }
  
  function addChild($name, $value = 0)
  {
	  $childObject = new JSONDataObject();
	  
	  $childObject->setName($name);
	  $childObject->setValue($value);
	  
	  $this->addChildObject($childObject);
  }
  
  function addChildObject($childObject)
  {
	  if ($this->m_childObjects == null)
	  {
		  $this->m_childObjects = array();
	  }
	  $this->m_childObjects[$this->m_nextChildIndex] = $childObject;
	  $this->m_nextChildIndex++;
  }
}

class JSONArrayObject extends JSONDataObject
{
	function __construct()
	{
		parent::__construct();
		
		$this->setEnclosingCharacters('[', ']');
	}
	
  function renderObject()
  {
  	$renderedText = '"' . $this->m_name . '"';
	$renderedText .= ":";
	$renderedText .= $this->m_enclosingCharacters[START_ENCLOSING_CHARACTER_INDEX];
	if ($this->m_childObjects != null)
	{
		for ($childIndex = 0; $childIndex < $this->m_nextChildIndex; $childIndex++)
		{
			if ($childIndex > 0)
			{
				$renderedText .= ",";
			}
			$renderedText .= "{";
			$renderedText .= $this->m_childObjects[$childIndex]->renderObject();
			$renderedText .= "}";
		}
    }
	$renderedText .= $this->m_enclosingCharacters[END_ENCLOSING_CHARACTER_INDEX];

    return $renderedText;
  }
}

class JSONPageData extends JSONDataObject
{
    var $m_directDisplay;           /*!< True to echo directly, false to return the page data */

    function __construct()
    {
        parent::__construct();

        $this->m_directDisplay = false; /* default to no direct display */
    }
    
    private function processChild(& $parent, $node)
    {
	}

    function loadJSONFile($fileName)
    {
    }
    
    function setDirectDisplay($directDisplay) { $this->m_directDisplay = $directDisplay; }
    function getDirectDisplay() { return $this->m_directDisplay; }
           
    function renderPage()
    {
	    $renderedText = "{";
	    $renderedText .= $this->renderObject();
	    $renderedText .= "}";
	    
	    if ($this->m_directDisplay == true)
	    {
		    echo $renderedText;
	    }
        return $renderedText;
    }
}

?>
