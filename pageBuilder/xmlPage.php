<?php
/*
 * Created on Oct 06, 2007
 *
 * Author: D.W. Milligan
 * Copyright: AFM Software 2007
 * Project: afmGallery
 * File: xmlPage.php
 */

define("XML_ATTRIBUTE_NAME", "0");
define("XML_ATTRIBUTE_VALUE", "1");

class XmlDataObject
{
  var $m_name;
  var $m_value;
  var $m_attrList;
  var $m_attrIndex;
  var $m_childDataObject;
  var $m_childIndex;

  public function __construct()
  {
    $this->m_name = null;
    $this->m_value = null;
    $this->m_attrList = array();
    $this->m_attrList[XML_ATTRIBUTE_NAME] = array();
    $this->m_attrList[XML_ATTRIBUTE_VALUE] = array();
    $this->m_attrIndex = 0;
    $this->m_childDataObject = array();
    $this->m_childIndex = 0;
  }

  function renderObject()
  {
  	$renderedText = "";
  	
  	if ($this->m_name != null)
  	{
    	$renderedText = "<";
    	$renderedText .= $this->m_name;
  	
	    if ($this->m_attrIndex > 0)
	    {
	      # Add the attributes if any
	      for ($attrIndex = 0; $attrIndex < $this->m_attrIndex; $attrIndex++)
	      {
	        $renderedText .= " ";
	        $renderedText .= $this->m_attrList[XML_ATTRIBUTE_NAME][$attrIndex];
	        $renderedText .= "=\"";
	        $renderedText .= $this->m_attrList[XML_ATTRIBUTE_VALUE][$attrIndex];
	        $renderedText .= "\"";
	      }
	      $renderedText .= ">";
	    }
	    else
	    {
	      $renderedText .= ">";
	    }
  	}
    if ($this->m_value !== null)
    {
      $renderedText .= $this->m_value;
    }

    for ($childIndex = 0; $childIndex < $this->m_childIndex; $childIndex++)
    {
      $renderedText .= $this->m_childDataObject[$childIndex]->renderObject();
    }

  	if ($this->m_name !== null)
  	{
	    $renderedText .= "</";
	    $renderedText .= $this->m_name;
	    $renderedText .= ">";
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

  function addAttribute($attributeName, $attributeValue)
  {
    $this->m_attrList[XML_ATTRIBUTE_NAME][$this->m_attrIndex] = $attributeName;
    $this->m_attrList[XML_ATTRIBUTE_VALUE][$this->m_attrIndex] = $attributeValue;
    $this->m_attrIndex++;
  }

  function getAttributeCount()
  {
    return $this->m_attrIndex;
  }

  function getAttributeName($attrIndex)
  {
    if ($attrIndex < $this->m_attrIndex)
    {
      return $this->m_attrList[XML_ATTRIBUTE_NAME][$attrIndex];
    }
    else
    {
      return "Invalid";
    }
  }

  function getAttributeValue($attrIndex)
  {
    if ($attrIndex < $this->m_attrIndex)
    {
      return $this->m_attrList[XML_ATTRIBUTE_VALUE][$attrIndex];
    }
    else
    {
      return "Invalid";
    }
  }
  
  /*
   * addChild
   * 
   * to add a child to the current node
   * 
   * @param - $name - the name of the child node
   * @param - $value - the value for the child node if any
   * 
   * @return - the newly added/created child node
   */
  function addChild($name, $value)
  {
  	$childDataObject = new XmlDataObject();
  	$childDataObject->setName($name);
  	$childDataObject->setValue($value);
  	
  	$this->addChildObject($childDataObject);
  	
  	return $childDataObject;
  }

  function addChildObject($childDataObject)
  {
    $this->m_childDataObject[$this->m_childIndex] = $childDataObject;
    $this->m_childIndex++;
  }

  function getChildCount()
  {
    return $this->m_childIndex;
  }

  function getChildObject($childIndex)
  {
    if ($childIndex < $this->m_childIndex)
    {
      return $this->m_childDataObject[$childIndex];
    }
    else
    {
      return "none";
    }
  }
  
  function findChild($name)
  {
  	$childObject = array();
  	
  	foreach ($this->m_childDataObject as $childDataObject)
  	{
  		if ($childDataObject->getName() == $name)
  		{
  			$childObject[] = $childDataObject;
  		}
  		else
  		{
  			if ($childDataObject->getChildCount() > 0)
  			{
  				$subchildObject = $childDataObject->findChild($name);
  				if ($subchildObject != null)
  				{
  					/* Merge will overwrite keys and this array is all of the same type */
  					foreach ($subchildObject as $subChild)
  					{
  						$childObject[] = $subChild;
  					}
  				}
  			}
  		}
  	}
  	
  	if (count($childObject) == 0)
  	{
  		$childObject = null;
  	}
  	return $childObject;
  }
};

class XmlPageData extends XmlDataObject
{
    var $m_startSession;            /*!< true to start a session on this page */
    var $m_languageSelection;       /*!< The language to specify */
    var $m_directDisplay;           /*!< True to echo directly, false to return the page data */

    public function __construct()
    {
        parent::__construct();
        $this->m_languageSelection = "EN";
        $this->m_directDisplay = true; /* default to direct display */
    }
    
    private function processChild(& $parent, $node)
    {
	    	/*
	    	 *  1 XML_ELEMENT_NODE
 2 XML_ATTRIBUTE_NODE
 3 XML_TEXT_NODE
 4 XML_CDATA_SECTION_NODE
 5 XML_ENTITY_REFERENCE_NODE
 6 XML_ENTITY_NODE
 7 XML_PROCESSING_INSTRUCTION_NODE
 8 XML_COMMENT_NODE
 9 XML_DOCUMENT_NODE
10 XML_DOCUMENT_TYPE_NODE
11 XML_DOCUMENT_FRAGMENT_NODE
12 XML_NOTATION_NODE
*/
    	if ($node != null)
    	{
    		$nodeName = $node->nodeName;
    		if ($node->prefix != NULL)
    		{
    			$nodeName = substr($node->nodeName, strpos($nodeName, ':') + 1);
//    			print $nodeName . "<br/>";
    		}
	        $childElement = new XmlDataObject();
	        switch ($node->nodeType)
	        {
	        	case XML_ELEMENT_NODE:
        		{
//        			print "Element: " . $node->nodeName . "<br/>";
        			$childElement->setName($nodeName);
        		}
        		break;
	        	case XML_ATTRIBUTE_NODE:
	        	{
//        			print "Attribute: " . $node->nodeName . "<br/>";
        			$parent->addAttribute($nodeName, $node->nodeValue);
        		}
        		break;
	        	case XML_TEXT_NODE:
	        	case XML_CDATA_SECTION_NODE:
	        	{
//        			print "Text: " . $node->nodeValue . "<br/>";
	        		$childElement->setValue($node->nodeValue);
        		}
        		break;
	        	default:
        		{
//        			print "NodeType: " . $node->nodeType . " node name: " . $node->nodeName . "<br/>";
        		}
        		break;
	        }

	        if ($node->hasChildNodes())
	        {
		        foreach ($node->childNodes as $childNode)
		        {
			    	$this->processChild($childElement, $childNode);
	            }
	        }
	        
	        if ($node->hasAttributes())
	        {
	        	foreach ($node->attributes as $childNode)
	        	{
	        		$this->processChild($childElement, $childNode);
	        	}
	        }
	        $parent->addChildObject($childElement);
    	}
    }

    function loadXmlFile($fileName)
    {
      /* Open the file for reading */
      $xmlFile = new DOMDocument();
      $xmlFile->load($fileName);

      $topNode = $xmlFile->documentElement;
      if ($topNode != null)
      {
      	$this->processChild($this, $topNode);
      }
    }
    
    function setDirectDisplay($directDisplay) { $this->m_directDisplay = $directDisplay; }
    function getDirectDisplay() { return $this->m_directDisplay; }
    
    function setLanguageSelection($languageSelection)
    {
        $this->m_languageSelection = $languageSelection;
    }
    
    function getLanguageSelection()
    {
        return $this->m_languageSelection;
    }
        
    function renderPage()
    {
        $renderedPage = <<<DOC_TYPE_SNIPPET
<?xml version="1.0" encoding="ISO-8859-1" ?>
DOC_TYPE_SNIPPET;

        $renderedPage .= "\n";

        $renderedPage .= $this->renderObject();

        # If they want us to dump then do it otherwise return it
        if ($this->m_directDisplay)
        {
            # Set the appropriate headers for this document
            header("Content-Type: application/xhtml+xml;charset=iso-8859-1");

            echo $renderedPage;
        }
        else
        {
            return $renderedPage;
        }
    }
};

?>
